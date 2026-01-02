<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyWebhookController;
use App\Http\Controllers\Webhooks\GorgiasWebhookController;

/*
|--------------------------------------------------------------------------
| API Routes - Webhooks
|--------------------------------------------------------------------------
|
| Here we register webhook routes that don't require CSRF protection.
| These are used for external services like Gorgias to send data.
|
*/

// Gorgias webhook endpoints
Route::post('/webhooks/gorgias/tickets', [GorgiasWebhookController::class, 'handleTicket'])
  ->name('webhooks.gorgias.tickets');

Route::get('/webhooks/gorgias/verify', [GorgiasWebhookController::class, 'verify'])
  ->name('webhooks.gorgias.verify');

  
Route::post('/store/product-created', [ShopifyWebhookController::class, 'productCreate']);
Route::post('/store/product-updated', [ShopifyWebhookController::class, 'productUpdate']);
Route::post('/store/product-deleted', [ShopifyWebhookController::class, 'productDelete']);

Route::post('/store/order-created', [ShopifyWebhookController::class, 'orderCreate']);
Route::post('/store/order-updated', [ShopifyWebhookController::class, 'orderUpdate']);
Route::post('/store/order-fullfilled', [ShopifyWebhookController::class, 'orderFullfilled']);
Route::post('/store/order-cancelled', [ShopifyWebhookController::class, 'orderCancelled']);
Route::post('/store/refunded', [ShopifyWebhookController::class, 'refundedCreated']);

Route::post('/store/customer-created', [ShopifyWebhookController::class, 'customerCreate']);
Route::post('/store/customer-updated', [ShopifyWebhookController::class, 'customerUpdate']);
Route::post('/store/customer-deleted', [ShopifyWebhookController::class, 'customerDelete']);

Route::post('/store/discount-created', [ShopifyWebhookController::class, 'discountCreate']);
Route::post('/store/discount-updated', [ShopifyWebhookController::class, 'discountUpdated']);
Route::post('/store/discount-deleted', [ShopifyWebhookController::class, 'discountDeleted']);