<?php

namespace App\Providers;

use App\Models\DocumentLoan;
use Illuminate\Support\Facades\Event;
use App\Models\CorrespondenceTransfer;
use Illuminate\Auth\Events\Registered;
use App\Observers\DocumentLoanObserver;
use App\Observers\CorrespondenceTransferObserver;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
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
        DocumentLoan::observe(DocumentLoanObserver::class);
        CorrespondenceTransfer::observe(CorrespondenceTransferObserver::class);
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
