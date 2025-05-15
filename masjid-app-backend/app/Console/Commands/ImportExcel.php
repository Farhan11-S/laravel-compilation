<?php

namespace App\Console\Commands;

use App\Imports\JemaahImport;
use Illuminate\Console\Command;

class ImportExcel extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Laravel Excel importer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->output->title('Starting import');
        (new JemaahImport)->withOutput($this->output)->import('jemaah.xlsx', 'local');
        $this->output->success('Import successful');
    }
}
