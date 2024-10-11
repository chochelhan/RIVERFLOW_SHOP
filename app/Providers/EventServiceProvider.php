<?php

namespace App\Providers;

use App\Events\OrderHistoryEvent;
use App\Listeners\OrderHistorySubscriber;
use App\Events\InventoryHistoryEvent;
use App\Listeners\InventoryHistorySubscriber;
use App\Events\MailEvent;
use App\Listeners\MailSubscriber;
use App\Events\SmsEvent;
use App\Listeners\SmsSubscriber;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        OrderHistoryEvent::class=> [
            OrderHistorySubscriber::class,
        ],
        InventoryHistoryEvent::class=> [
            InventoryHistorySubscriber::class,
        ],
        MailEvent::class=> [
            MailSubscriber::class,
        ],
        SmsEvent::class=> [
           SmsSubscriber::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     *
     * @return bool
     */
    public function shouldDiscoverEvents()
    {
        return false;
    }
}
