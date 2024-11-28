<?php

use Illuminate\Support\Facades\Route;
use Modules\Invoices\Presentation\Http\InvoicesController;
use Ramsey\Uuid\Validator\GenericValidator;

Route::pattern('invoice_id', (new GenericValidator)->getPattern());

Route::group(['prefix' => 'invoice'], function () {
   Route::post('/', [InvoicesController::class, 'create'])->name('invoices.create');
   Route::post('/send', [InvoicesController::class, 'send'])->name('invoices.send');
   Route::get('/{invoice_id}', [InvoicesController::class, 'view'])->name('invoices.view');
});
