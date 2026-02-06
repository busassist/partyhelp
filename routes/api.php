<?php

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
