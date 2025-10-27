<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Dispatch;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');
        $payments = Payment::with('dispatch.client')
            ->when($status, fn($q)=>$q->where('payment_status',$status))
            ->orderByDesc('id')->paginate(20);

        return view('payments.index', compact('payments','status'));
    }

    public function storeOrUpdate(Request $request, Dispatch $dispatch)
    {
        $validated = $request->validate([
            'payment_status' => 'required|in:Paid,Unpaid,Advance Received',
            'advance_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'nullable|numeric|min:0',
            'remarks' => 'nullable|string|max:500',
        ]);

        $payment = $dispatch->payment;
        $payment->update($validated);

        return back()->with('success','Payment updated');
    }
}
