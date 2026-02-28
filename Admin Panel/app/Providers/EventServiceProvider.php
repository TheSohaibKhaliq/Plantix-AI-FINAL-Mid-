<?php

namespace App\Providers;

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
        // ── Expert & Agency Events ────────────────────────────────────────────
        \App\Events\Expert\AppointmentStatusChanged::class => [
            \App\Listeners\Expert\SendAppointmentStatusNotification::class,
        ],

        \App\Events\Expert\ExpertMentionedInForum::class => [
            \App\Listeners\Expert\SendForumMentionNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }
}

