<?php

namespace App\Console\Commands;

use App\Models\CouponUsage;
use Illuminate\Console\Command;

class CouponUsageEnded extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'coupon-usage:ended';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'update coupon type when the coupon is ended';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $date = date("Y-m-d h:i:s");
        $couponUsages = CouponUsage::whereDate('ended_at', '<', $date)
            ->where('status', 'active')
            ->get();

        foreach ($couponUsages as $cu) {
            $cu->status = 'deactive';
            $cu->save();

            $cu->user()
                ->update([
                    'package_type' => 0
                ]);
        }
    }
}
