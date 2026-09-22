<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public const STATUSES = ['pending', 'paid', 'processing', 'shipped', 'delivered', 'cancelled'];

    public function index(): View
    {
        return view('admin.orders', ['orders' => Order::with('user', 'payment', 'items')->latest()->paginate(15)]);
    }

    public function show(Order $order): View
    {
        return view('admin.orders.show', [
            'order' => $order->load('user', 'payment', 'items.product'),
            'statuses' => self::STATUSES,
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate(['status' => ['required', 'string', 'in:'.implode(',', self::STATUSES)]]);
        $order->update($data);

        return back()->with('success', 'Order status updated.');
    }
}
