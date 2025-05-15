<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionTransactionStatus;
use App\Models\SubscriptionTransaction;
use Illuminate\Console\Command;

class SubscriptionTransactionEnded extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription-transaction:ended';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update subscription type when the subscription is ended';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = date("Y-m-d h:i:s");
        $trxs = SubscriptionTransaction::whereDate('ended_at', '<', $date)
            ->where('status', SubscriptionTransactionStatus::SELESAI)
            ->get();

        foreach ($trxs as $trx) {
            $trx->status = SubscriptionTransactionStatus::TIDAK_AKTIF;
            $trx->save();
            $trx->createdBy()
                ->update([
                    'package_type' => 0
                ]);
        }
    }
}
