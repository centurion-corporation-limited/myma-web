<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\AddFlexmMerchant::class,
        Commands\CreatePayout::class,
        Commands\CreateTrip::class,
        Commands\UpdateAds::class,
        Commands\SendNotification::class,
        Commands\SpuulCheckSubscription::class,
        Commands\SpuulSubscribe::class,
        Commands\UploadRemittance::class,
        Commands\UploadSettlement::class,
        Commands\UploadWallet::class,
        Commands\FlexmRegistration::class,
        Commands\FlexmListener::class,
        Commands\SingxListener::class,
        Commands\CateringPayout::class,
        
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('create:trip')->everyMinute();
        $schedule->command('create:payout')->everyMinute();
        $schedule->command('update:ads')->everyMinute();
        $schedule->command('send:notification')->everyMinute();
        $schedule->command('spuul:check')->dailyAt("12:00");
        $schedule->command('spuul:subscribe')->dailyAt("01:00");
        $schedule->command('flexm:create')->hourly();
        $schedule->command('upload:remittance')->hourly();
        $schedule->command('upload:settlement')->hourly();
        $schedule->command('upload:wallet')->hourly();
        $schedule->command('flexm:listener')->twiceDaily(7,13);
        $schedule->command('singx:listener')->twiceDaily(7,13);
        // $schedule->command('flexm:register')->hourly();
        // $schedule->command('flexm:register')->everyMinute();
        // $schedule->call(function () {
        //     \Log::debug("i was called");
        // })->everyMinute();
        // $schedule->command('inspire')
        //          ->hourly();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        // $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
