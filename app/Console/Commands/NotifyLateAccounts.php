<?php

namespace App\Console\Commands;

use App\Models\Account;
use App\Models\NotificationLog;
use Illuminate\Console\Command;

class NotifyLateAccounts extends Command
{
    protected $signature = 'notify:late-accounts';
    protected $description = 'Envia aviso de contas vencidas';

    public function handle()
    {
        $late = Account::where('status', 'pending')
            ->where('due_date', '<', now())
            ->get();

        foreach ($late as $acc) {
            $message = "A conta '{$acc->title}' venceu em {$acc->due_date} e ainda não foi paga.";

            // Aqui você envia a mensagem (WhatsApp, SMS, Telegram, E-mail)
            // Exemplo WhatsApp com API Baileys, UltraMSG, Z-API etc.
            // WhatsAppService::send($message);

            NotificationLog::create([
                'account_title' => $acc->title,
                'due_date'      => $acc->due_date,
                'message'       => $message
            ]);
        }

        $this->info("Mensagens enviadas.");
    }
}

