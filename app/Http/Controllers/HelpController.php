<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HelpController extends Controller
{
    public function show(): View
    {
        return view('help');
    }
}
