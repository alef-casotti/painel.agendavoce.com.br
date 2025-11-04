<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FinanceiroController extends Controller
{
    /**
     * Exibir área financeira
     */
    public function index()
    {
        return view('financeiro.index');
    }
}
