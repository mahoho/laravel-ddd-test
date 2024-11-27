<?php

use Modules\Invoices\Infrastructure\Providers\InvoiceEventsListenerServiceProvider;
use Modules\Invoices\Infrastructure\Providers\InvoiceServiceProvider;

return [
    \Modules\Notifications\Infrastructure\Providers\NotificationServiceProvider::class,
    InvoiceServiceProvider::class,
    InvoiceEventsListenerServiceProvider::class,
];
