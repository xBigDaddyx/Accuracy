<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Xbigdaddyx\Accuracy\Controller\SearchController;
use Xbigdaddyx\Accuracy\Controller\VerificationController;

Route::middleware(['web', 'auth'])->prefix('accuracy')->group(function () {

    Route::get('/carton/check', [SearchController::class, 'index'])->name('accuracy.check.carton.release');
    Route::get('/{carton}/polybag', [VerificationController::class, 'index'])->name('accuracy.validation.polybag.release');
    Route::get('/{carton}/completed', [VerificationController::class, 'completed'])->name('accuracy.completed.carton.release');
});
