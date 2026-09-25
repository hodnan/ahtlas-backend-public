<?php

use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;


$hora = Carbon::parse('01:00')->format('H:i');

// cron ([minutos] [horas] [dias do mês] [mês] [dias da semana])

Schedule::command('ai:emmployee')->timezone('America/Sao_Paulo')->cron('0 1 5,20 * *'); // roda todo dia 5 e 20 do mês as 1h
Schedule::command('users:avatar')->timezone('America/Sao_Paulo')->cron('0 0 * * 0'); // roda todo domingo as 00:00

Schedule::command('user:update')->timezone('America/Sao_Paulo')->dailyAt($hora)
    ->then(function () {
        Artisan::call('employee:data');
        Artisan::call('employee:daily-data');
        Artisan::call('employee:sectorn1');
        Artisan::call('employee:sectorn2');
        Artisan::call('employee:sectorn1n2');
        Artisan::call('employee:sectorn1-manager');
        if (app()->environment('production')) {
            Artisan::call('hc:calc');
            Artisan::call('fincance:employee-hc');
        }
    });

if (app()->environment('production')) {
    Schedule::command('kpi:result-guilda')->timezone('America/Sao_Paulo')->dailyAt('23:00');
}

Schedule::command('kpi:result')->timezone('America/Sao_Paulo')->dailyAt('02:00')
    ->then(function () {
        Artisan::call('kpi:sector');
        Artisan::call('kpi:sector-daily');
        Artisan::call('kpi:employee');
        // RV
        Artisan::call('rv:approve');
        Artisan::call('rv:term-to-signature');
        Artisan::call('rv:evaluation');

        // Central de Controle
        Artisan::call('cdc:result-sector');
    });

// Portal de trocas
Schedule::command('tradetime:conclude')->timezone('America/Sao_Paulo')->dailyAt('06:00')
    ->then(function () {
        Artisan::call('tradetime:cancel');
    });

// RV
// Schedule::command('rv:replicate')->timezone('America/Sao_Paulo')->monthlyOn(10, '01:00');
Schedule::command('rv:evaluation-stop')->timezone('America/Sao_Paulo')->monthlyOn(15, '11:00');
// incluir aprovação automática 

// Central de Controle 
Schedule::command('cdc:reply')->timezone('America/Sao_Paulo')->monthlyOn(1, '01:00');
Schedule::command('cdc:stage')->timezone('America/Sao_Paulo')->weekdays()->at('04:00');


// Reports
Schedule::command('report:bulletin-hh')->timezone('America/Sao_Paulo')->cron('20,25,50,55  * * * *');
Schedule::command('report:bulletin-backoffice')->timezone('America/Sao_Paulo')->cron('30 * * * *');


if (app()->environment('production')) {
    Schedule::command('telegram:send')->timezone('America/Sao_Paulo')->everyMinute();
}

// Artisan::command('inspire', function () {
//     $this->comment(Inspiring::quote());
// })->purpose('Display an inspiring quote')->hourly();
