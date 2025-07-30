<?php

namespace App\Console\Commands;

use App\Services\DovecotPasswordService;
use Illuminate\Console\Command;

class SetUserPassword extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:dps';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Password :informix ");

        $dp = DovecotPasswordService::hash('informix');

        $this->info("Decode password :{$dp}");

    }
}
