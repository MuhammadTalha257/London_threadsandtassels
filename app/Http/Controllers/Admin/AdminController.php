<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        return view('admin.index', [
            'productCount' => Product::count(),
            'newArrivalCount' => Product::where('is_new_arrival', true)->count(),
            'customerCount' => User::where('is_admin', false)->count(),
            'paymentTotal' => Payment::where('status', 'completed')->sum('amount'),
            'lowStockCount' => Product::where('is_active', true)->whereBetween('stock', [1, 3])->count(),
            'recentOrders' => Order::with('user', 'payment')->latest()->take(6)->get(),
        ]);
    }
}
