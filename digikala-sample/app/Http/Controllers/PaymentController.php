<?php

namespace App\Http\Controllers;

use App\Enums\Status;
use App\Http\Requests\PaymentRequest;
use App\Models\Cart;
use App\Models\Payment;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

/**
 * Class PaymentController for handling the user payment.
 *
 * @package App\Http\Controllers
 */
class PaymentController extends Controller
{
    /**
     * Handling the creation page view.
     *
     * @param Cart $cart
     * @return View|RedirectResponse
     */
    public function create(Cart $cart)
    {
        if (!Gate::check('payable-item', [$cart])) {
            return redirect()->route('carts.index');
        }

        $addresses = Auth::user()->addresses;

        return view('utils.payment.index')
            ->with('addresses', $addresses)
            ->with('cart', $cart)
            ->with('user', Auth::user());
    }

    /**
     * The index page is the payment page.
     *
     * @param Payment $payment
     * @return View|RedirectResponse
     */
    public function show(Payment $payment)
    {
        return view('utils.payment.show')
            ->with('payment', $payment);
    }

    /**
     * The pay method for handling the payment functionality.
     *
     * @param PaymentRequest $request
     * @param Cart $cart the current cart
     * @return RedirectResponse
     */
    public function store(PaymentRequest $request, Cart $cart): RedirectResponse
    {
        $validated = $request->validated();
        $status = true;
        $total = 0;

        foreach ($cart->orders as $order) {
            if ($order->number > $order->item->number) {
                $status = false;
            }
            $total += $order->number * $order->item->price;
        }

        DB::transaction(function () use ($validated, $cart, $status, $total) {
            if ($status) {
                Payment::query()
                    ->create([
                        'cart_id' => $cart->id,
                        'amount' => $total,
                        'bank' => $validated['bank']
                    ]);

                foreach ($cart->orders as $order) {
                    $order->item
                        ->update([
                            'number' => $order->item->number - $order->number
                        ]);
                    $order->save();
                }

                $cart->update([
                    'status' => Status::READY()
                ]);
                $cart->save();
            } else {
                $cart->update([
                    'status' => Status::STORE_FAIL()
                ]);
                $cart->save();
            }

            Auth::user()->update([
               'cart_id' => null
            ]);

            Auth::user()->save();
        });

        return redirect()
            ->route('cart.index');
    }
}
