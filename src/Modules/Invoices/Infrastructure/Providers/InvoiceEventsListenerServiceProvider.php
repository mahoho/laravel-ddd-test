<?php

namespace Modules\Invoices\Infrastructure\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider;
use Modules\Invoices\Application\Listeners\ChangeInvoiceStatusOnSuccessfulDelivery;
use Modules\Notifications\Api\Events\ResourceDeliveredEvent;

class InvoiceEventsListenerServiceProvider extends EventServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        ResourceDeliveredEvent::class => [
            ChangeInvoiceStatusOnSuccessfulDelivery::class
        ]
    ];

}
