<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        if ($cart === []) {
            return redirect()->route('store.index')->with('success', 'Your bag is ready for something special.');
        }

        $user = $request->user();
        $lastOrder = $user->latestOrder();

        return view('checkout.create', [
            'cart' => $cart,
            'total' => collect($cart)->sum('line_total'),
            'savedAddress' => $user->address ?: $lastOrder?->shipping_address,
            'savedPhone' => $user->phone ?: $lastOrder?->shipping_phone,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'shipping_name' => ['required', 'string', 'max:120'],
            'shipping_email' => ['required', 'email'],
            'shipping_phone' => ['nullable', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:500'],
        ]);
        $cart = $request->session()->get('cart', []);
        if ($cart === []) {
            return redirect()->route('cart.index');
        }

        $order = DB::transaction(function () use ($cart, $data, $request): Order {
            $subtotal = collect($cart)->sum('line_total');
            $order = Order::create([...$data, 'user_id' => $request->user()->id, 'order_number' => 'LTT-'.strtoupper(Str::random(8)), 'subtotal' => $subtotal, 'shipping' => 0, 'total' => $subtotal, 'status' => 'paid']);
            foreach ($cart as $line) {
                $product = Product::lockForUpdate()->findOrFail($line['product_id']);
                if ($product->stock < $line['quantity']) {
                    abort(422, $product->name.' no longer has enough stock.');
                }
                $product->decrement('stock', $line['quantity']);
                $order->items()->create([...$line, 'product_name' => $line['name'], 'unit_price' => $line['price']]);
            }
            Payment::create(['order_id' => $order->id, 'provider' => 'paypal', 'provider_reference' => 'DEMO-'.strtoupper(Str::random(12)), 'amount' => $subtotal, 'currency' => 'GBP', 'status' => 'completed', 'paid_at' => now()]);

            return $order;
        });

        // Remember these details on the shopper's account so a returning visit is pre-filled.
        $request->user()->forceFill([
            'phone' => $data['shipping_phone'] ?: $request->user()->phone,
            'address' => $data['shipping_address'],
        ])->save();

        $request->session()->forget('cart');

        return redirect()->route('orders.show', $order)->with('success', 'Payment received — thank you for your order.');
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id || $request->user()->is_admin, 403);

        return view('checkout.show', ['order' => $order->load('items', 'payment')]);
    }
}
