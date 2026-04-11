<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    DashboardController,
    ClientController,
    DispatchController,
    ReturnController,
    PaymentController,
    InvoiceController,
    OrderController,
    QuotationController,
    AgreementController,
    ItemController,
    MonthlySubscriptionController,
    CompanySettingsController,
};
use App\Http\Controllers\SuperAdmin\{
    AuthController as SuperAuthController,
    DashboardController as SuperDashboardController,
    CompanyController as SuperCompanyController,
    PlanController as SuperPlanController,
    SubscriptionInvoiceController as SuperBillingController,
};

// ─── Super Admin Routes ───────────────────────────────────────────────────────
Route::prefix('super-admin')->name('super.')->group(function () {
    Route::get('login',  [SuperAuthController::class, 'showLogin'])->name('login');
    Route::post('login', [SuperAuthController::class, 'login'])->name('login.post');
    Route::post('logout',[SuperAuthController::class, 'logout'])->name('logout');

    Route::middleware('super.admin')->group(function () {
        Route::get('dashboard', [SuperDashboardController::class, 'index'])->name('dashboard');

        Route::resource('companies', SuperCompanyController::class)->names('companies');
        Route::post('companies/{company}/toggle-status', [SuperCompanyController::class, 'toggleStatus'])->name('companies.toggle-status');

        Route::resource('plans', SuperPlanController::class)->names('plans');

        Route::get('billing',                              [SuperBillingController::class, 'index'])->name('billing.index');
        Route::post('billing',                             [SuperBillingController::class, 'store'])->name('billing.store');
        Route::post('billing/{invoice}/mark-paid',         [SuperBillingController::class, 'markPaid'])->name('billing.mark-paid');
    });
});

// ─── Subscription Expired Page ────────────────────────────────────────────────
Route::get('/subscription-expired', fn() => view('subscription.expired'))->name('subscription.expired');

// ─── Public / Auth Routes ─────────────────────────────────────────────────────
Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

// ─── Tenant (Company) Routes ──────────────────────────────────────────────────
Route::middleware(['auth', 'company.subscription'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Company Settings
    Route::get('company/settings',  [CompanySettingsController::class, 'edit'])->name('company.settings');
    Route::post('company/settings', [CompanySettingsController::class, 'update'])->name('company.settings.update');
    Route::get('company/plan',      [CompanySettingsController::class, 'plan'])->name('company.plan');

    Route::resource('clients', ClientController::class);
    Route::resource('items', ItemController::class);
    Route::get('/items-data', [ItemController::class, 'getData'])->name('items.data');
    Route::get('/items-search', [ItemController::class, 'search'])->name('items.search');
    Route::resource('quotations', QuotationController::class);
    Route::post('quotations/{quotation}/generate-pdf',   [QuotationController::class, 'generatePdf'])->name('quotations.generatePdf');
    Route::post('quotations/{quotation}/send-email',     [QuotationController::class, 'sendEmail'])->name('quotations.sendEmail');
    Route::post('quotations/{quotation}/send-whatsapp',  [QuotationController::class, 'sendWhatsapp'])->name('quotations.sendWhatsapp');

    Route::resource('orders', OrderController::class);
    Route::get('orders-search-clients', [OrderController::class, 'searchClients'])->name('orders.searchClients');
    Route::get('orders/create',  [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders',        [OrderController::class, 'store'])->middleware('plan.limit:orders')->name('orders.store');
    Route::post('/orders/{order}/complete',        [OrderController::class, 'complete'])->name('orders.complete');
    Route::post('orders/{order}/generate-pdf',     [OrderController::class, 'generatePdf'])->name('orders.generatePdf');
    Route::post('orders/{order}/send-email',       [OrderController::class, 'sendEmail'])->name('orders.sendEmail');
    Route::post('orders/{order}/send-whatsapp',    [OrderController::class, 'sendWhatsapp'])->name('orders.sendWhatsapp');
    Route::post('/orders/{order}/settle',          [OrderController::class, 'settle'])->name('orders.settle');
    Route::post('/orders/{order}/agreement',       [OrderController::class, 'generateAgreement'])->name('orders.generateAgreement');
    Route::post('/orders/{order}/upload-aadhaar',  [OrderController::class, 'uploadAadhaar'])->name('orders.uploadAadhaar');
    Route::post('/orders/{order}/agreement/send-email',     [AgreementController::class, 'sendEmail'])->name('orders.sendAgreementEmail');
    Route::get('/orders/{order}/agreement/send-whatsapp',   [AgreementController::class, 'sendWhatsapp'])->name('orders.sendAgreementWhatsapp');
    Route::post('quotations/{quotation}/convert',  [OrderController::class, 'storeFromQuotation'])->name('quotations.convertToOrder');
    Route::post('/orders/{order}/send-reminder',   [PaymentController::class, 'sendReminder'])->name('orders.send-reminder');
    Route::post('/orders/{order}/record-payment',  [PaymentController::class, 'recordPayment'])->name('orders.record-payment');
    Route::get('/orders/{order}/payment-history',  [PaymentController::class, 'getPaymentHistory'])->name('orders.payment-history');

    Route::resource('subscriptions', MonthlySubscriptionController::class);
    Route::get('subscriptions/{subscription}/generate-invoice',    [MonthlySubscriptionController::class, 'generateInvoice'])->name('subscriptions.generate-invoice');
    Route::post('monthly-invoice/{invoice}/send',                  [MonthlySubscriptionController::class, 'sendInvoice'])->name('monthly-invoice.send');
    Route::post('monthly-invoice/{invoice}/send-reminder',         [MonthlySubscriptionController::class, 'sendReminder'])->name('monthly-invoice.send-reminder');
    Route::post('monthly-invoice/{invoice}/mark-paid',             [MonthlySubscriptionController::class, 'markPaid'])->name('monthly-invoice.mark-paid');
    Route::delete('monthly-invoice/{invoice}/delete',              [MonthlySubscriptionController::class, 'deleteInvoice'])->name('monthly-invoice.delete');
    Route::get('subscriptions/client/{id}',                        [MonthlySubscriptionController::class, 'getClientData'])->name('subscriptions.client-data');

    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::resource('dispatches', DispatchController::class)->only(['index','create','store','show','destroy']);
    Route::post('returns', [ReturnController::class, 'store'])->name('returns.store');
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoice-generate', [InvoiceController::class, 'generate'])->name('invoices.generate');
    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments/{dispatch}', [PaymentController::class, 'storeOrUpdate'])->name('payments.store');
});

// ─── Signed / Public Download Routes ─────────────────────────────────────────
Route::get('quotations/{hash}/download',        [QuotationController::class, 'download'])->name('quotations.download')->middleware('signed');
Route::get('/orders/{hash}/download',           [OrderController::class, 'download'])->name('orders.download')->middleware('signed');
Route::get('/agreement/sign/{code}',            [AgreementController::class, 'show'])->name('agreement.sign');
Route::post('/agreement/{code}',                [AgreementController::class, 'submit'])->name('agreement.submit');
Route::get('/monthly-invoice/{hash}/download',  [MonthlySubscriptionController::class, 'downloadInvoice'])->name('monthly-invoice.download')->middleware('signed');

