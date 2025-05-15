<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionTransactionStatus;
use App\Models\SubscriptionTransaction;
use Illuminate\Console\Command;

class SubscriptionTransactionExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription-transaction:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change subscription to GAGAL if expired_at already passed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = date("Y-m-d h:i:s");
        SubscriptionTransaction::whereDate('expired_at', '<', $date)
            ->where('status', SubscriptionTransactionStatus::BELUM_SELESAI)
            ->update([
                'status' => SubscriptionTransactionStatus::GAGAL
            ]);
    }
}
