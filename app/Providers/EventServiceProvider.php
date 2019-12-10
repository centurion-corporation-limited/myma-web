<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Illuminate\Auth\Events\Logout' => [
            'App\Listeners\LogSuccessfulLogout@logout',
        ],
        'App\Events\VerifyEmailEvent' => [
            'App\Listeners\VerifyEmailListener',
        ],
        'App\Events\AccountCreated' => [
            'App\Listeners\AccountCreatedListener',
        ],
        'App\Events\NotifyFlexmRegistration' => [
            'App\Listeners\FlexmListener',
        ],
        'App\Events\SendOTP' => [
            'App\Listeners\SendOtpListener',
        ],
        'App\Events\FeedbackReplyEvent' => [
            'App\Listeners\FeedbackListener',
        ],
        'App\Events\FeedbackNotification' => [
            'App\Listeners\FeedbackNotificationListener',
        ],
        'App\Events\MaintenanceCreatedEvent' => [
            'App\Listeners\MaintenanceCreatedListener',
        ],
        'App\Events\MaintenanceStatus' => [
            'App\Listeners\MaintenanceStatusListener',
        ],
        'App\Events\SendFeedbackEvent' => [
            'App\Listeners\SendFeedbackListener',
        ],
        'App\Events\SendBrowserNotification' => [
            'App\Listeners\NotificationListener',
        ],
        'App\Events\NotifyAdmin' => [
            'App\Listeners\AdminListener',
        ],
        'App\Events\UpdatePrice' => [
            'App\Listeners\PriceListener',
        ]
    ];

    // protected $subscribe = [
    //     'App\Listeners\UserListener',
    // ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        // Event::listen('user.created',                             'App\Listeners\UserListener@onCreated');
        Event::listen('user.confirm-success',                     'App\Listeners\UserListener@onConfirmSuccess');
        // Event::listen('user.send_otp',                            'App\Listeners\UserListener@onAccountCreated');

        // Event::listen('user.inform_account',                       'App\Listeners\UserListener@onAccountCreated');

        // Event::listen('feedback.reply',                            'App\Listeners\FeedbackListener@onReply');
        // Event::listen('feedback.notification',                     'App\Listeners\FeedbackListener@onNotification');

        // Event::listen('maintenance.created',                       'App\Listeners\UserListener@onMaintenanceCreate');
        // Event::listen('maintenance.status',                        'App\Listeners\UserListener@onMaintenanceUpdate');

        Event::listen('order.created',                             'App\Listeners\OrderListener@onCreated');
    }
}
