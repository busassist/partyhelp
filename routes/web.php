<?php

use App\Http\Controllers\FunctionPackController;
use App\Http\Controllers\LeadPurchaseController;
use App\Http\Controllers\MediaServeController;
use App\Http\Controllers\StripeWebhookController;
use App\Http\Controllers\TwilioWhatsAppWebhookController;
use App\Http\Controllers\VenueApprovalController;
use App\Http\Controllers\VenueBillingController;
use App\Http\Controllers\VenueSetPasswordController;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Additional services selection (customer-facing, from +72h email; signed link)
Route::get('/p/additional-services/{lead}', [\App\Http\Controllers\AdditionalServicesController::class, 'show'])
    ->name('additional-services.show')
    ->middleware('signed');
Route::post('/p/additional-services/{lead}', [\App\Http\Controllers\AdditionalServicesController::class, 'submit'])
    ->name('additional-services.submit')
    ->middleware('signed');

// Admin: download additional service submissions as CSV
Route::get('/admin/additional-service-submissions/csv', [\App\Http\Controllers\AdditionalServiceSubmissionController::class, 'csv'])
    ->name('admin.additional-service-submissions.csv')
    ->middleware('auth');

// Function pack download (customer-facing, dark theme)
Route::get('/function-pack/{token}', [FunctionPackController::class, 'show'])
    ->name('function-pack.show');
Route::get('/function-pack/{token}/download', [FunctionPackController::class, 'download'])
    ->name('function-pack.download');

// Lead purchase link (from venue email)
Route::get('/lead/{lead}/purchase/{venue}', [LeadPurchaseController::class, 'show'])
    ->name('lead.purchase.show')
    ->middleware('signed');

// Venue set password (from email when admin creates venue)
Route::get('/venue/set-password', [VenueSetPasswordController::class, 'show'])
    ->name('venue.set-password');
Route::post('/venue/set-password', [VenueSetPasswordController::class, 'store'])
    ->name('venue.set-password.store');

Route::view('/venue/registration-received', 'venue.registration-received')
    ->name('venue.registration-received');

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

// Stripe webhook (no CSRF)
Route::post('stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->name('stripe.webhook')
    ->withoutMiddleware([VerifyCsrfToken::class]);

// Twilio WhatsApp webhook (button replies for lead opportunity Accept/Ignore)
Route::post('webhook/twilio/whatsapp', [TwilioWhatsAppWebhookController::class, 'handleIncoming'])
    ->name('twilio.whatsapp.webhook')
    ->withoutMiddleware([VerifyCsrfToken::class]);

// Venue billing (auth required)
Route::middleware(['auth'])->prefix('venue')->name('venue.')->group(function () {
    Route::post('billing/create-checkout-session', [VenueBillingController::class, 'createCheckoutSession'])
        ->name('billing.create-checkout-session');
    Route::get('billing/create-setup-session', [VenueBillingController::class, 'createSetupSession'])
        ->name('billing.create-setup-session');
    Route::post('billing/update-auto-topup', [VenueBillingController::class, 'updateAutoTopup'])
        ->name('billing.update-auto-topup');
    Route::post('billing/disable-auto-topup', [VenueBillingController::class, 'disableAutoTopup'])
        ->name('billing.disable-auto-topup');
});
