<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SuporteApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas de Suporte (API para clientes) - Requer token de autenticação
Route::prefix('suporte')->middleware('suporte.token')->group(function () {
    // Cliente envia mensagem (cria ticket se não existir)
    Route::post('/mensagem', [SuporteApiController::class, 'enviarMensagem']);
    
    // Lista todos os tickets de um cliente (por email)
    Route::get('/tickets/{email}', [SuporteApiController::class, 'listarTickets']);
    
    // Lista todas as mensagens de um ticket específico
    Route::get('/ticket/{ticket_id}/mensagens', [SuporteApiController::class, 'listarMensagens']);
    
    // Marcar mensagem específica como visualizada
    Route::post('/mensagem/{message_id}/visualizar', [SuporteApiController::class, 'marcarVisualizada']);
    
    // Marcar todas as mensagens de um ticket como visualizadas
    Route::post('/ticket/{ticket_id}/visualizar', [SuporteApiController::class, 'marcarTicketVisualizado']);
});
