<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Quotation;
use Illuminate\Support\Facades\Storage;
use PDF; // Using barryvdh/laravel-dompdf alias

class PdfService
{
    /**
     * Generates the order confirmation/invoice PDF.
     */
    public function generateOrderPdf(Order $order): string
    {
        $order->load('items');
        $html = view('orders.pdf', compact('order'))->render();
        $pdf = PDF::loadHTML($html)->setPaper('a4', 'portrait');

        $fileName = $order->order_code . '.pdf';
        $path = 'orders/' . $fileName;
        
        Storage::disk('public')->put($path, $pdf->output());

        $publicPath = 'storage/' . $path;
        
        $order->update(['pdf_path' => $publicPath]);

        return $publicPath;
    }

    /**
     * Generates the settlement PDF for an order.
     */
    public function generateSettlementPdf(Order $order): string
    {
        $order->load('items');
        // Original logic uses orders.pdf for settlement as well
        $html = view('orders.pdf', compact('order'))->render();
        $pdf = PDF::loadHTML($html)->setPaper('a4', 'portrait');

        $fileName = $order->order_code . '-invoice.pdf';
        $path = 'orders/' . $fileName;
        
        Storage::disk('public')->put($path, $pdf->output());

        $publicPath = 'storage/' . $path;

        $order->update(['settlement_pdf_path' => $publicPath]);

        return $publicPath;
    }

    /**
     * Generates a combined dues PDF for multiple orders.
     */
    public function generateCombinedDuesPdf(Order $order, $ordersList, $company): \Barryvdh\DomPDF\PDF
    {
        $html = view('orders.combined-dues-pdf', ['orders' => $ordersList, 'company' => $company])->render();
        return PDF::loadHTML($html)->setPaper('a4', 'portrait');
    }

    /**
     * Generates the quotation PDF.
     */
    public function generateQuotationPdf(Quotation $quotation): string
    {
        $quotation->load('items');
        $html = view('quotations.pdf', compact('quotation'))->render();
        $pdf = PDF::loadHTML($html)->setPaper('a4', 'portrait');

        $fileName = $quotation->code . '.pdf';
        $path = 'quotations/' . $fileName;

        Storage::disk('public')->put($path, $pdf->output());

        $publicPath = 'storage/' . $path;

        $quotation->update(['pdf_path' => $publicPath]);

        return $publicPath;
    }
}
