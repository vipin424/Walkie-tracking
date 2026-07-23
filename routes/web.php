<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    ClientController,
    PaymentController,
    OrderController,
    OrderReturnController,
    QuotationController,
    AgreementController,
    ItemController,
    MonthlySubscriptionController,
    SubscriptionAgreementController,
};

use App\Http\Controllers\Storefront\HomeController;

// ─────────────────────────────────────────────────────────────────────────────
// Public Storefront Routes
// ─────────────────────────────────────────────────────────────────────────────

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/category/{slug}', [HomeController::class, 'category'])->name('storefront.category');
Route::get('/category/{category_slug}/{item_slug}', [HomeController::class, 'product'])->name('storefront.product');

// Storefront AJAX APIs
Route::post('/api/storefront/check-availability', [App\Http\Controllers\StorefrontAPIController::class, 'checkAvailability'])->name('api.storefront.check-availability');

// Signed download links (outside auth — accessed from email)
Route::get('quotations/{hash}/download', [QuotationController::class, 'download'])
    ->name('quotations.download')->middleware('signed');

Route::get('/orders/{hash}/download', [OrderController::class, 'download'])
    ->name('orders.download')->middleware('signed');

Route::get('/monthly-invoice/{hash}/download', [MonthlySubscriptionController::class, 'downloadInvoice'])
    ->name('monthly-invoice.download')->middleware('signed');

// Client-facing agreement signing (no auth required)
Route::get('/agreement/sign/{code}',  [AgreementController::class, 'show'])->name('agreement.sign');
Route::post('/agreement/{code}',      [AgreementController::class, 'submit'])->name('agreement.submit');

Route::get('/subscription-agreement/sign/{code}',  [SubscriptionAgreementController::class, 'show'])->name('subscription-agreement.sign');
Route::post('/subscription-agreement/{code}',       [SubscriptionAgreementController::class, 'submit'])->name('subscription-agreement.submit');

// ─────────────────────────────────────────────────────────────────────────────
// Auth Profile Routes
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// ─────────────────────────────────────────────────────────────────────────────
// Admin Routes (auth protected)
// ─────────────────────────────────────────────────────────────────────────────

Route::middleware(['auth'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Clients ──────────────────────────────────────────────────────────────
    Route::resource('clients', ClientController::class);

    // ── Categories / Catalog ──────────────────────────────────────────────────
    Route::resource('categories', App\Http\Controllers\CategoryController::class);
    Route::get('/categories-data', [App\Http\Controllers\CategoryController::class, 'getData'])->name('categories.data');

    // ── Items / Catalog ──────────────────────────────────────────────────────
    Route::resource('items', ItemController::class);
    Route::get('/items-data',   [ItemController::class, 'getData'])->name('items.data');
    Route::get('/items-search', [ItemController::class, 'search'])->name('items.search');

    // ── Quotations ───────────────────────────────────────────────────────────
    Route::resource('quotations', QuotationController::class);
    Route::post('quotations/{quotation}/generate-pdf',  [QuotationController::class, 'generatePdf'])->name('quotations.generatePdf');
    Route::post('quotations/{quotation}/send-email',    [QuotationController::class, 'sendEmail'])->name('quotations.sendEmail');
    Route::post('quotations/{quotation}/send-whatsapp', [QuotationController::class, 'sendWhatsapp'])->name('quotations.sendWhatsapp');
    Route::post('quotations/{quotation}/convert',       [OrderController::class, 'storeFromQuotation'])->name('quotations.convertToOrder');

    // ── Orders ───────────────────────────────────────────────────────────────
    Route::resource('orders', OrderController::class);
    Route::get('orders-search-clients',                   [OrderController::class, 'searchClients'])->name('orders.searchClients');
    Route::post('/orders/{order}/complete',               [OrderController::class, 'complete'])->name('orders.complete');
    Route::post('orders/{order}/generate-pdf',            [OrderController::class, 'generatePdf'])->name('orders.generatePdf');
    Route::get('orders/{order}/settlement-invoice',       [OrderController::class, 'generateSettlementPdf'])->name('orders.settlementPdf');
    Route::get('orders/{order}/combined-dues-pdf',        [OrderController::class, 'generateCombinedDuesPdf'])->name('orders.combinedDuesPdf');
    Route::post('orders/{order}/send-email',              [OrderController::class, 'sendEmail'])->name('orders.sendEmail');
    Route::post('orders/{order}/send-whatsapp',           [OrderController::class, 'sendWhatsapp'])->name('orders.sendWhatsapp');
    Route::post('/orders/{order}/settle',                 [OrderController::class, 'settle'])->name('orders.settle');
    Route::post('/orders/{order}/agreement',              [OrderController::class, 'generateAgreement'])->name('orders.generateAgreement');
    Route::post('/orders/{order}/upload-aadhaar',         [OrderController::class, 'uploadAadhaar'])->name('orders.uploadAadhaar');
    Route::post('/orders/{order}/agreement/send-email',   [AgreementController::class, 'sendEmail'])->name('orders.sendAgreementEmail');
    Route::get('/orders/{order}/agreement/send-whatsapp', [AgreementController::class, 'sendWhatsapp'])->name('orders.sendAgreementWhatsapp');

    // ── Payments ──────────────────────────────────────────────────────────────
    Route::post('/orders/{order}/send-reminder',    [PaymentController::class, 'sendReminder'])->name('orders.send-reminder');
    Route::post('/orders/{order}/record-payment',   [PaymentController::class, 'recordPayment'])->name('orders.record-payment');
    Route::get('/orders/{order}/payment-history',   [PaymentController::class, 'getPaymentHistory'])->name('orders.payment-history');

    // ── Returns ───────────────────────────────────────────────────────────────
    Route::post('/orders/{order}/record-return',                        [OrderReturnController::class, 'store'])->name('orders.record-return');
    Route::delete('/orders/{order}/return-item/{returnItem}',           [OrderReturnController::class, 'destroy'])->name('orders.return-item.destroy');

    // ── Monthly Subscriptions ────────────────────────────────────────────────
    Route::resource('subscriptions', MonthlySubscriptionController::class);
    Route::get('subscriptions/{subscription}/generate-invoice',         [MonthlySubscriptionController::class, 'generateInvoice'])->name('subscriptions.generate-invoice');
    Route::post('monthly-invoice/{invoice}/send',                       [MonthlySubscriptionController::class, 'sendInvoice'])->name('monthly-invoice.send');
    Route::post('monthly-invoice/{invoice}/send-reminder',              [MonthlySubscriptionController::class, 'sendReminder'])->name('monthly-invoice.send-reminder');
    Route::post('monthly-invoice/{invoice}/mark-paid',                  [MonthlySubscriptionController::class, 'markPaid'])->name('monthly-invoice.mark-paid');
    Route::delete('monthly-invoice/{invoice}/delete',                   [MonthlySubscriptionController::class, 'deleteInvoice'])->name('monthly-invoice.delete');
    Route::get('subscriptions/client/{id}',                             [MonthlySubscriptionController::class, 'getClientData'])->name('subscriptions.client-data');

    // ── Subscription Agreements ──────────────────────────────────────────────
    Route::post('subscriptions/{subscription}/agreement/generate',      [SubscriptionAgreementController::class, 'generate'])->name('subscriptions.agreement.generate');
    Route::post('subscriptions/{subscription}/agreement/send-email',    [SubscriptionAgreementController::class, 'sendEmail'])->name('subscriptions.agreement.send-email');
    Route::get('subscriptions/{subscription}/agreement/send-whatsapp',  [SubscriptionAgreementController::class, 'sendWhatsapp'])->name('subscriptions.agreement.send-whatsapp');

});
