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
        'App\Events\RepaymentCreated' => [
            'App\Listeners\SendRepaymentNotifications',
            'App\Listeners\CheckPreviousRepayments',
        ],
        'App\Events\RepaymentUpdated' => [
            'App\Listeners\RefreshRepaymentTransactions',
        ],
        'App\Events\RepaymentReversed' => [
            'App\Listeners\RefreshRepaymentReversed',
        ],
        'App\Events\LoanTransactionUpdated' => [
            'App\Listeners\UpdateLoanTransaction',
        ],
        'App\Events\InterestWaived' => [
            'App\Listeners\UpdateInterestWaived',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
        //
    }
}