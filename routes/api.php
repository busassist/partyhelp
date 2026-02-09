<?php

use App\Http\Controllers\PartyhelpFormConfigController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
| These routes receive form submissions from the WordPress/Elementor site.
| They are CSRF-exempt (handled in api middleware group).
*/

Route::post('/webhook/elementor-lead', [WebhookController::class, 'handleElementorLead'])
    ->name('webhook.elementor-lead');

/*
|--------------------------------------------------------------------------
| Partyhelp Form Config API (for WordPress plugin sync)
|--------------------------------------------------------------------------
| Returns areas, occasion types, guest brackets, budget ranges for form rendering.
*/

Route::prefix('partyhelp-form')->group(function () {
    Route::get('/config', [PartyhelpFormConfigController::class, 'index'])->name('partyhelp-form.config');
    Route::get('/areas', [PartyhelpFormConfigController::class, 'areas'])->name('partyhelp-form.areas');
    Route::get('/occasion-types', [PartyhelpFormConfigController::class, 'occasionTypes'])->name('partyhelp-form.occasion-types');
    Route::get('/guest-brackets', [PartyhelpFormConfigController::class, 'guestBrackets'])->name('partyhelp-form.guest-brackets');
    Route::get('/budget-ranges', [PartyhelpFormConfigController::class, 'budgetRanges'])->name('partyhelp-form.budget-ranges');
});
