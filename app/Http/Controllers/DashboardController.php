<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Exibir o dashboard principal
     */
    public function index()
    {
        $user = auth()->user();
        return view('dashboard', compact('user'));
    }
}
