<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderAgreement;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AgreementController extends Controller
{
    public function show($code)
    {
        $agreement = OrderAgreement::where('agreement_code', $code)->firstOrFail();

        abort_if(now()->greaterThan($agreement->expires_at), 403);

        return view('agreement.sign', compact('agreement'));
    }

    public function submit(Request $request, $code)
    {
        $agreement = OrderAgreement::where('agreement_code', $code)->firstOrFail();
        //dd($agreement);
        // Safety checks
        abort_if($agreement->status === 'signed', 403);
        abort_if(now()->greaterThan($agreement->expires_at), 403);

        $request->validate([
            'signature' => 'required|string',
        ]);

        /** 🔹 SAVE SIGNATURE IMAGE */
        $signatureData = $request->signature;
        $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
        $signatureData = base64_decode($signatureData);

        $signaturePath = 'signatures/' . $agreement->agreement_code . '.png';
        Storage::disk('public')->put($signaturePath, $signatureData);

        $agreement->update([
            'signature_image' => $signaturePath,
            'signed_at' => now(),
            'status' => 'signed',        
        ]);
        //dd($agreement);
        return redirect()->back()->with('agreement_signed', true);
    }

}
