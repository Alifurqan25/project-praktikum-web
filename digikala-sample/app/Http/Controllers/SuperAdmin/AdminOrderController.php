<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class AdminOrderController for managing orders.
 *
 * @package App\Http\Controllers\SuperAdmin
 */
class AdminOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return View
     */
    public function index(): View
    {
        $orders = Order::paginate(6);

        return view('admin.payment.index')
            ->with('orders', $orders);
    }

    /**
     * Handling the order modification.
     *
     * @param Request $request
     * @param Order $order
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Order $order
     * @return RedirectResponse
     */
    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()
            ->route('admin-order.index');
    }
}
