<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        return view('admin.customers', [
            'customers' => User::where('is_admin', false)->withCount('orders')->latest()->paginate(15),
        ]);
    }

    public function show(User $customer): View
    {
        abort_if($customer->is_admin, 404);

        return view('admin.customers.show', [
            'customer' => $customer,
            'orders' => $customer->orders()->with('items', 'payment')->latest()->get(),
        ]);
    }
}
