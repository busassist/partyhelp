<?php

use App\Http\Controllers\FunctionPackController;
use App\Http\Controllers\LeadPurchaseController;
use App\Http\Controllers\MediaServeController;
use App\Http\Controllers\VenueApprovalController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Function pack download (customer-facing, dark theme)
Route::get('/function-pack/{token}', [FunctionPackController::class, 'show'])
    ->name('function-pack.show');
Route::get('/function-pack/{token}/download', [FunctionPackController::class, 'download'])
    ->name('function-pack.download');

// Lead purchase link (from venue email)
Route::get('/lead/{lead}/purchase/{venue}', [LeadPurchaseController::class, 'show'])
    ->name('lead.purchase.show')
    ->middleware('signed');

// Venue approval actions (from admin email)
Route::get('/venue-approval/{venue}/approve', [VenueApprovalController::class, 'approve'])
    ->name('venue.approve')
    ->middleware('signed');
Route::get('/venue-approval/{venue}/reject', [VenueApprovalController::class, 'reject'])
    ->name('venue.reject')
    ->middleware('signed');

Route::get('/media/{path}', [MediaServeController::class, 'show'])
    ->where('path', '.*')
    ->name('media.serve');
