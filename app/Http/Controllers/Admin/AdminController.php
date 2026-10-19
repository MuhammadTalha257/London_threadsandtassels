<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

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
            'isInMaintenance' => app()->isDownForMaintenance(),
        ]);
    }

    /**
     * Close the shop to visitors and show the maintenance page. The admin is
     * sent through a secret link that sets a bypass cookie, so they can keep
     * using the site and turn it back on.
     */
    public function maintenanceOn(): RedirectResponse
    {
        $secret = Str::random(40);
        Artisan::call('down', ['--secret' => $secret, '--render' => 'errors::503', '--refresh' => 60]);

        return redirect('/'.$secret);
    }

    public function maintenanceOff(): RedirectResponse
    {
        Artisan::call('up');

        return redirect()->route('admin.index')->with('success', 'The shop is open again.');
    }
}
