<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoices\Presentation\Http\InvoicesController;
use Ramsey\Uuid\Validator\GenericValidator;

Route::pattern('invoice_id', (new GenericValidator)->getPattern());

Route::group(['prefix' => 'invoice'], function () {
   Route::post('/', [InvoicesController::class, 'create']);
   Route::post('/send', [InvoicesController::class, 'send']);
   Route::get('/{invoice_id}', [InvoicesController::class, 'view']);
});
