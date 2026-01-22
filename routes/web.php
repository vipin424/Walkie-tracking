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
    AgreementController
};

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

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('clients', ClientController::class);
    Route::resource('quotations', QuotationController::class);
    // Extra custom routes for PDF, Email, WhatsApp
    Route::post('quotations/{quotation}/generate-pdf', [QuotationController::class, 'generatePdf'])->name('quotations.generatePdf');
    Route::post('quotations/{quotation}/send-email', [QuotationController::class, 'sendEmail'])->name('quotations.sendEmail');
    Route::post('quotations/{quotation}/send-whatsapp', [QuotationController::class, 'sendWhatsapp'])->name('quotations.sendWhatsapp');
    //Route::get('quotations/{quotation}/download',[QuotationController::class, 'download'])->name('quotations.download');

    Route::resource('orders', OrderController::class);
        // Direct order create
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/orders/{order}/complete',[OrderController::class,'complete'])->name('orders.complete');
    Route::post('orders/{order}/generate-pdf', [OrderController::class, 'generatePdf'])->name('orders.generatePdf');
    Route::post('orders/{order}/send-email', [OrderController::class, 'sendEmail'])->name('orders.sendEmail');
    Route::post('orders/{order}/send-whatsapp', [OrderController::class, 'sendWhatsapp'])->name('orders.sendWhatsapp');
    Route::post('/orders/{order}/settle',[OrderController::class,'settle'])->name('orders.settle');
    Route::post('/orders/{order}/agreement',[OrderController::class, 'generateAgreement'])->name('orders.generateAgreement');
    Route::post('/orders/{order}/upload-aadhaar',[OrderController::class, 'uploadAadhaar'])->name('orders.uploadAadhaar');
    Route::post('/orders/{order}/agreement/send-email', [AgreementController::class, 'sendEmail'])->name('orders.sendAgreementEmail');
    Route::get('/orders/{order}/agreement/send-whatsapp', [AgreementController::class, 'sendWhatsapp'])->name('orders.sendAgreementWhatsapp');
    // Quotation → Order
    Route::post(
        'quotations/{quotation}/convert',
        [OrderController::class, 'storeFromQuotation']
    )->name('quotations.convertToOrder');


    // View
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    // Route::post('/orders/{order}/approve', [OrderController::class, 'approve'])->name('orders.approve');
    // Route::post('/orders/{order}/reject', [OrderController::class, 'reject'])->name('orders.reject');
    // Route::get('/orders/{order}/convert-dispatch', [OrderController::class, 'convertToDispatch'])
    //     ->name('orders.convertToDispatch');
    Route::resource('dispatches', DispatchController::class)->only(['index','create','store','show','destroy']);
    Route::post('returns', [ReturnController::class, 'store'])->name('returns.store');
    Route::resource('invoices', InvoiceController::class);
    Route::post('invoice-generate', [InvoiceController::class, 'generate'])->name('invoices.generate');


    Route::get('payments', [PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments/{dispatch}', [PaymentController::class, 'storeOrUpdate'])->name('payments.store');
});
    Route::get('quotations/{quotation}/download',[QuotationController::class, 'download'])->name('quotations.download');
    Route::get('/orders/{order}/download',[OrderController::class, 'download'])->name('orders.download')->middleware('signed');
    Route::get('/agreement/sign/{code}',[AgreementController::class, 'show'])->name('agreement.sign');
    Route::post('/agreement/{code}', [AgreementController::class, 'submit'])->name('agreement.submit');
