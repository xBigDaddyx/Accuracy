<?php

use Illuminate\Support\Facades\Route;
use Xbigdaddyx\Accuracy\Controller\CartonNumberController;
use Xbigdaddyx\Accuracy\Controller\PurchaseOrderController;

Route::get('/carton/number', CartonNumberController::class)->name('api.carton-number');
Route::get('/carton/po', PurchaseOrderController::class)->name('api.carton-po');
