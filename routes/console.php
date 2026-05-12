<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Disparo mensal de cobrança via WhatsApp — roda todo dia à 08:00
// O próprio comando verifica se hoje é o dia configurado por tenant
Schedule::command('cobranca:disparo-mensal')->dailyAt('08:00');
