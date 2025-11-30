<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Os comandos Artisan customizados da aplicação.
     *
     * @var array<int, class-string>
     */
    protected $commands = [
        \App\Console\Commands\NotifyLateAccounts::class,
    ];

    /**
     * Defina a programação de tarefas do aplicativo.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Executa o comando de notificação de contas vencidas a cada minuto
        $schedule->command('notify:late-accounts')
            ->everyMinute()
            ->onOneServer()              // evita duplicidade em ambientes com cluster
            ->withoutOverlapping()       // evita travamentos
            ->runInBackground();         // executa sem travar o scheduler
    }

    /**
     * Registra os comandos Artisan para a aplicação.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
