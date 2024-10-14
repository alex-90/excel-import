<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use App\Services\ExcelReader;

class ImportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:csv {--test}';
    
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import CSV File';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $test = $this->option('test');
        
        if ($test) {
            $this->info('Test mode enabled!');
        }

        $path = Storage::path('upload/stock.csv');
        $reader = new ExcelReader($path, $test);

        $res = $reader->parse();
        $added = $reader->save();
        
        $res['errors']->map(function($err){
            $this->error($err);
        });

        $total = $res['errors']->count() + $added;

        $this->info($total . " rows processed, " . $added . ' rows inserted, '. $res['errors']->count() . ' was skipped');
    }
}
