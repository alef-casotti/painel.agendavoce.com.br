<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FinanceiroController;
use App\Http\Controllers\SuporteController;
use App\Http\Controllers\BuscaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Rotas de autenticação
Route::middleware(['guest'])->group(function () {
    Route::get('/', [LoginController::class, 'showLoginForm'])->name('login');
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Rotas protegidas
Route::middleware(['auth'])->group(function () {
    // Dashboard principal
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Área Admin (apenas admin)
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');
        
        // Gerenciamento de Usuários
        Route::resource('users', UserController::class);
    });

    // Área Financeiro (admin e financeiro)
    Route::middleware(['role:admin,financeiro'])->prefix('financeiro')->name('financeiro.')->group(function () {
        Route::get('/', [FinanceiroController::class, 'index'])->name('index');
    });

    // Área Suporte (admin e suporte)
    Route::middleware(['role:admin,suporte'])->prefix('suporte')->name('suporte.')->group(function () {
        Route::get('/', [SuporteController::class, 'index'])->name('index');
        Route::get('/ticket/{id}', [SuporteController::class, 'visualizar'])->name('visualizar');
        Route::post('/ticket/{id}/responder', [SuporteController::class, 'responder'])->name('responder');
        Route::post('/ticket/{id}/fechar', [SuporteController::class, 'fechar'])->name('fechar');
    });

    // Busca global
    Route::get('/busca', [BuscaController::class, 'index'])->name('busca.index'); 

    // Perfil do usuário
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::put('/', [ProfileController::class, 'update'])->name('update');
        Route::put('/password', [ProfileController::class, 'updatePassword'])->name('password.update');
    });
});
