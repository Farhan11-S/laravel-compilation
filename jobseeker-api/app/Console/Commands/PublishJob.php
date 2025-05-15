<?php

namespace App\Console\Commands;

use App\Models\Job;
use Illuminate\Console\Command;

class PublishJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:publish-job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'publish job draft';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Job::whereDate('published_at', '<=', now())
            ->update([
                'published_at' => null,
                'created_at' => now(),
            ]);
    }
}
