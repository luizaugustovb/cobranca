<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BotController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rotas do Robô WhatsApp (Viicio) — aceita CPF e CNPJ
Route::post('/bot/consultar-documento', [BotController::class, 'consultarDocumento']);
Route::post('/bot/consultar-cpf',       [BotController::class, 'consultarCpf']); // alias retrocompatível
