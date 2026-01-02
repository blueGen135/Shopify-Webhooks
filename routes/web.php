<?php

use App\Livewire\Dashboard;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Auth\ForgotPassword;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShopifyController;
use App\Http\Controllers\Auth\AuthController;

// Guest routes
Route::middleware('guest')->group(function () {
  Route::get('/', Login::class)->name('login');

  Route::get('/forgot-password', ForgotPassword::class)->name('password.request');

  Route::get('/reset-password/{token}', ResetPassword::class)->name('password.reset');
});


// Authenticated routes
Route::middleware('auth')->group(function () {
  Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
  Route::get('/dashboard', Dashboard::class)->name('dashboard');

  Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
    Route::get('/', \App\Livewire\Users\Index::class)
      ->middleware('permission:users.view')
      ->name('index');

    Route::get('create', \App\Livewire\Users\Create::class)
      ->middleware('permission:users.create')
      ->name('create');

    // !users can edit themselves, so middleware moved to Livewire component
    Route::get('{user}/edit', \App\Livewire\Users\Edit::class)->name('edit');
  });

  Route::group(['prefix' => 'roles'], function () {
    Route::get('/', \App\Livewire\Roles\Index::class)
      ->middleware('permission:roles.view')
      ->name('roles.index');

    Route::get('create', \App\Livewire\Roles\Create::class)
      ->middleware('permission:roles.create')
      ->name('roles.create');

    Route::get('{role}/edit', \App\Livewire\Roles\Edit::class)
      ->middleware('permission:roles.edit')
      ->name('roles.edit');
  });

  Route::group(['prefix' => 'permissions'], function () {
    Route::get('/', \App\Livewire\Permissions\Index::class)
      ->middleware('role:superadmin')
      ->name('permissions.index');

    Route::get('create', \App\Livewire\Permissions\Create::class)
      ->middleware('role:superadmin')
      ->name('permissions.create');

    Route::get('{permission}/edit', \App\Livewire\Permissions\Edit::class)
      ->middleware('role:superadmin')
      ->name('permissions.edit');
  });

  Route::group(['prefix' => 'warehouses', 'as' => 'warehouses.'], function () {
    Route::get('/', \App\Livewire\Warehouses\Index::class)
      ->middleware('permission:warehouses.manage')
      ->name('index');
  });

  Route::group(['prefix' => 'settings', 'as' => 'settings.'], function () {
    Route::get('/gorgias', \App\Livewire\Settings\Gorgias::class)
      ->middleware('permission:settings.manage')
      ->name('gorgias');

    Route::get('/smart-assist', \App\Livewire\Settings\SmartAssist::class)
      ->middleware('permission:settings.manage')
      ->name('smart-assist');

    Route::get('/shopify', \App\Livewire\Settings\Shopify::class)
      ->middleware('permission:settings.manage')
      ->name('shopify');

    Route::get('/fedex', \App\Livewire\Settings\Fedex::class)
      ->middleware('permission:settings.manage')
      ->name('fedex');

    Route::get('/odoo', \App\Livewire\Settings\Odoo::class)
      ->middleware('permission:settings.manage')
      ->name('odoo');
  });

  Route::group(['prefix' => 'tickets', 'as' => 'tickets.'], function () {
    Route::get('/', \App\Livewire\Tickets\Index::class)
      ->middleware('permission:tickets.view')
      ->name('index');

    Route::get('/details/{ticket}', \App\Livewire\Tickets\Details::class)
      ->middleware('permission:tickets.view')
      ->name('details');
  });

  Route::get('/store/products', [ShopifyController::class, 'index'])->name('shopify.products');
Route::get('/store/add-product', [ShopifyController::class, 'createProduct']);
Route::get('/shopify/import-products', [ShopifyController::class, 'import'])->name('shopify.import');
Route::get('/all-product', [ShopifyController::class, 'importedProducts'])->name('shopify.db-products');
Route::get('/shopify/create-product', [ShopifyController::class, 'addNewProduct'])->name('shopify.create-product');
Route::post('/shopify/store-product', [ShopifyController::class, 'storeProduct'])->name('shopify.store-product');
Route::get('/shopify/products/{id}', [ShopifyController::class, 'viewProduct'])->name('shopify.product.show');


Route::get('/store/orders', [ShopifyController::class, 'getOrders'])->name('shopify.orders');
Route::get('/shopify/import-orders', [ShopifyController::class, 'importOrders'])->name('shopify.import.orders');
Route::get('/shopify/orders', [ShopifyController::class, 'getOrders'])->name('shopify.orders');
Route::post('/orders/{order}/fulfill', [ShopifyController::class, 'fulfillOrder'])->name('orders.fulfill');
Route::post('/customers/{customer}/update', [ShopifyController::class, 'updateShopifyCustomer'])->name('customers.update');

// Customers

Route::get('/shopify/import-customers', [ShopifyController::class, 'importCustomers'])->name('shopify.import.customers');
Route::get('/shopify/customers', [ShopifyController::class, 'getCustomers'])->name('shopify.customers');
Route::get('/store/customers/{customer}', [ShopifyController::class, 'show'])->name('customers.show');
Route::post('/customers/{customer}/delete', [ShopifyController::class, 'destroyCustomer'])->name('customers.delete');

// Route::post('/customers/{customer}/orders/store', [ShopifyController::class, 'createOrder'])->name('customers.create-order');

// Create order for a specific customer
Route::get('/customers/{customer}/orders/create', [ShopifyController::class, 'createForCustomer'])->name('orders.createForCustomer');
Route::post('/customers/{customer}/orders/store', [ShopifyController::class, 'storeForCustomer'])->name('orders.storeForCustomer');
Route::get('/order/{order}', [ShopifyController::class, 'viewOrder'])->name('shopify.view-order');
Route::post('/orders/{order}/duplicate', [ShopifyController::class, 'duplicateOrder'])->name('orders.duplicate');
Route::post('/orders/{order}/cancel', [ShopifyController::class, 'cancelOrder'])->name('orders.cancel');
Route::get('/orders/{order}/refund', [ShopifyController::class, 'refundOrder'])->name('orders.refund.confirm');
Route::post('/orders/{order}/process-refund', [ShopifyController::class, 'processRefund'])->name('orders.refund.process');

Route::get('/orders/{order}/partial-refund', [ShopifyController::class, 'partialRefundForm'])->name('orders.refund.partial.form');
Route::post('/orders/{order}/refund-partial', [ShopifyController::class, 'partialRefund'])->name('orders.refund.partial');

// Edit and update order routes
Route::get('/orders/{order}/edit', [ShopifyController::class, 'editOrder'])->name('shopify.orders.edit');
Route::post('/orders/{order}/update', [ShopifyController::class, 'updateOrder'])->name('shopify.orders.update');


Route::get('webhooks/', [WebhookRegistrationController::class, 'index'])->name('webhooks.index');
Route::post('webhooks/register', [WebhookRegistrationController::class, 'register'])->name('webhooks.register');
Route::post('webhooks/unregister', [WebhookRegistrationController::class, 'unregister'])->name('webhooks.unregister');
Route::get('webhooks/status', [WebhookRegistrationController::class, 'status'])->name('webhooks.status');
Route::get('webhooks/test', [WebhookRegistrationController::class, 'test'])->name('webhooks.test');
Route::post('webhooks/test/{topic}', [WebhookRegistrationController::class, 'testWebhook'])->name('webhooks.test-webhook');

/** Coupons */
Route::get('/coupons', [ShopifyController::class, 'Coupons'])->name('shopify.all-coupons');
Route::get('/shopify/import-coupons', [ShopifyController::class, 'importCoupons'])->name('shopify.import-coupons');
Route::delete('/shopify/coupons/{coupon}',[ShopifyController::class, 'deleteCoupon'])->name('coupons.delete');
Route::get('/create-coupon', [ShopifyController::class, 'createCoupon'])->name('shopify.create-coupon');
Route::post('/shopify/store-coupon', [ShopifyController::class, 'storeCoupon'])->name('shopify.store-coupon');
});