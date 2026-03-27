<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\BotController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Rota de Consulta do Robô WhatsApp (Viicio)
Route::post('/bot/consultar-cpf', [BotController::class, 'consultarCpf']);
