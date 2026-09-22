<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function show(Request $request): View
    {
        return view('account', [
            'orders' => $request->user()->orders()->with('items', 'payment')->latest()->get(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $request->user()->forceFill($data)->save();

        return redirect()->route('account', ['tab' => 'profile'])->with('success', 'Your details have been updated.');
    }
}
