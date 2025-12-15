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
    QuotationController
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
    Route::get('quotations/{quotation}/download',[QuotationController::class, 'download'])->name('quotations.download');

    Route::resource('orders', OrderController::class);
        // Direct order create
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');

    // Quotation → Order
    Route::post(
        'quotations/{quotation}/convert-to-order',
        [OrderController::class, 'storeFromQuotation']
    )->name('orders.fromQuotation');

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
