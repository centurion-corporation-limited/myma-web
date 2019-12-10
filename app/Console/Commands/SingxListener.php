<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Mail;
use App\Mail\ExceptionOccured;

class SingxListener extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'singx:listener';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To check if singx server is working or not.';

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
     * @return mixed
     */
    public function handle()
    {
        try {
            $hostname = 'uat.singx.co';
            $port = 80;

            //\Log::debug('Attempting to connect to: '.$hostname.' '.$port);

            if(@fsockopen($hostname, $port, $code, $message, 5) === false) {
                throw new \Exception($message, $code);
            }

            // \Log::debug("Successfully connected to: %s:%u", $hostname, $port);
        }
        catch(\Exception $e) {
            \Log::debug("Error: ".$e->getCode().' - '.$e->getMessage());
            $html = "<p>Not able to connect to singx server. Error - ".$e->getCode().' - '.$e->getMessage();
            Mail::to('hemantnarang@innovativepeople.com')->send(new ExceptionOccured($html));
            // exit(1);
        }
    }
}
