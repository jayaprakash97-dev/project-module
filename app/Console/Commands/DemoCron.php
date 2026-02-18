<?php

namespace App\Console\Commands;

use App\Models\Task;
use Carbon\Carbon;


use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DemoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'demo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
    Log::info("Cron is working fine!");
        // Deletes records where due_date is today
    Task::whereDate('due_date', '>', Carbon::today())->delete();
    $this->info('Due tasks deleted successfully!');

    }
}
