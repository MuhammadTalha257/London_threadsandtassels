<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $request->session()->get('cart', []);

        return view('cart.index', ['cart' => $cart, 'total' => collect($cart)->sum('line_total')]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $data = $request->validate(['size' => ['required', 'string', 'max:12'], 'quantity' => ['required', 'integer', 'min:1', 'max:10']]);
        abort_unless($product->is_active && $product->stock >= $data['quantity'], 422);
        abort_unless(in_array($data['size'], $product->sizes, true), 422);

        $cart = $request->session()->get('cart', []);
        $key = $product->id.'-'.$data['size'];
        $quantity = min(($cart[$key]['quantity'] ?? 0) + $data['quantity'], $product->stock);
        $cart[$key] = ['product_id' => $product->id, 'name' => $product->name, 'size' => $data['size'], 'price' => (float) $product->effective_price, 'quantity' => $quantity, 'line_total' => $quantity * (float) $product->effective_price, 'image_url' => $product->photo];
        $request->session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Added to your bag.');
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        $data = $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:10']]);
        $cart = $request->session()->get('cart', []);
        abort_unless(isset($cart[$key]), 404);

        $product = Product::find($cart[$key]['product_id']);
        $quantity = $product ? min($data['quantity'], max((int) $product->stock, 1)) : $data['quantity'];
        $cart[$key]['quantity'] = $quantity;
        $cart[$key]['line_total'] = round($quantity * $cart[$key]['price'], 2);
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Bag updated.');
    }

    public function destroy(Request $request, string $key): RedirectResponse
    {
        $cart = $request->session()->get('cart', []);
        unset($cart[$key]);
        $request->session()->put('cart', $cart);

        return back()->with('success', 'Item removed.');
    }
}
