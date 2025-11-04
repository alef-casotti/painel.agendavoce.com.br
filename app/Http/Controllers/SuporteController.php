<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SuporteController extends Controller
{
    /**
     * Exibir área de suporte
     */
    public function index()
    {
        return view('suporte.index');
    }
}
