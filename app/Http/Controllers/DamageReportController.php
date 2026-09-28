<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\DamageReport;
use App\Models\MonthlySubscription;
use App\Models\PaymentTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use PDF;

class DamageReportController extends Controller
{
    // ─── INDEX ──────────────────────────────────────────────────────────────────
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DamageReport::with('client')
                ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
                ->orderByDesc('id');

            return datatables()->eloquent($query)
                ->addColumn('report_code', fn($r) => '<a href="'.route('damage-reports.show', $r).'" class="text-danger fw-semibold">'.$r->report_code.'</a>')
                ->addColumn('client_info', fn($r) => '<div><strong>'.$r->client_name.'</strong><br><small class="text-muted">'.$r->client_phone.'</small></div>')
                ->addColumn('damage_date', fn($r) => $r->damage_date->format('d M Y'))
                ->addColumn('amount', fn($r) => '₹'.number_format($r->replacement_amount, 2))
                ->addColumn('status_badge', function ($r) {
                    $map = ['pending' => 'warning', 'sent' => 'info', 'paid' => 'success'];
                    return '<span class="badge bg-'.$map[$r->status].' px-3 py-2">'.ucfirst($r->status).'</span>';
                })
                ->addColumn('actions', function ($r) {
                    return '
                    <div class="btn-group">
                        <a href="'.route('damage-reports.show', $r).'" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                        <a href="'.route('damage-reports.edit', $r).'" class="btn btn-sm btn-outline-warning"><i class="bi bi-pencil"></i></a>
                    </div>';
                })
                ->rawColumns(['report_code', 'client_info', 'status_badge', 'actions'])
                ->make(true);
        }

        return view('damage-reports.index');
    }

    // ─── CREATE ──────────────────────────────────────────────────────────────────
    public function create()
    {
        $clients       = Client::orderBy('name')->get();
        $subscriptions = MonthlySubscription::where('status', 'active')->with('client')->orderByDesc('id')->get();
        return view('damage-reports.create', compact('clients', 'subscriptions'));
    }

    // ─── STORE ───────────────────────────────────────────────────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'client_id'            => 'required|exists:clients,id',
            'subscription_id'      => 'nullable|exists:monthly_subscriptions,id',
            'walkie_units'         => 'nullable|array',
            'walkie_units.*.model' => 'nullable|string|max:191',
            'walkie_units.*.serial'=> 'nullable|string|max:191',
            'walkie_units.*.unit_id'=> 'nullable|string|max:191',
            // Accept either checkbox reasons OR a pre-built hidden description
            'damage_reasons'       => 'required|array',
            'damage_reasons.*'     => 'string|max:100',
            'damage_description'   => 'nullable|string',   // built client-side from reasons
            'damage_extra_note'    => 'nullable|string',
            'damage_date'          => 'required|date',
            'damage_photos'        => 'nullable|array',
            'damage_photos.*'      => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            'replacement_amount'   => 'required|numeric|min:0',
            'billing_details'      => 'nullable|string',
            'notes'                => 'nullable|string',
        ]);

        $client = Client::findOrFail($request->client_id);

        // Upload photos
        $photoPaths = [];
        if ($request->hasFile('damage_photos')) {
            Storage::disk('public')->makeDirectory('damage-photos');
            foreach ($request->file('damage_photos') as $photo) {
                $path = $photo->store('damage-photos', 'public');
                $photoPaths[] = $path;
            }
        }

        // Clean up walkie_units to remove empty rows
        $walkieUnits = collect($request->walkie_units)->filter(function ($unit) {
            return !empty($unit['model']) || !empty($unit['serial']) || !empty($unit['unit_id']);
        })->values()->toArray();

        $report = DamageReport::create([
            'report_code'          => DamageReport::generateCode(),
            'client_id'            => $client->id,
            'subscription_id'      => $request->subscription_id ?: null,
            'client_name'          => $client->name,
            'client_phone'         => $client->contact_number,
            'client_email'         => $client->email ?? null,
            'billing_details'      => $request->billing_details,
            'walkie_units'         => !empty($walkieUnits) ? $walkieUnits : null,
            'damage_description'   => $request->damage_description,
            'damage_date'          => $request->damage_date,
            'damage_photos'        => $photoPaths ?: null,
            'replacement_amount'   => $request->replacement_amount,
            'notes'                => $request->notes,
            'status'               => 'pending',
        ]);

        // Auto-generate PDF
        $this->generatePdf($report);

        return redirect()->route('damage-reports.show', $report)
            ->with('success', 'Damage Report created and invoice PDF generated successfully.');
    }

    // ─── SHOW ────────────────────────────────────────────────────────────────────
    public function show(DamageReport $damageReport)
    {
        $damageReport->load('client', 'subscription');
        return view('damage-reports.show', compact('damageReport'));
    }

    // ─── EDIT ────────────────────────────────────────────────────────────────────
    public function edit(DamageReport $damageReport)
    {
        $clients       = Client::orderBy('name')->get();
        $subscriptions = MonthlySubscription::where('status', 'active')->with('client')->orderByDesc('id')->get();
        return view('damage-reports.edit', compact('damageReport', 'clients', 'subscriptions'));
    }

    // ─── UPDATE ──────────────────────────────────────────────────────────────────
    public function update(Request $request, DamageReport $damageReport)
    {
        $request->validate([
            'walkie_units'         => 'nullable|array',
            'walkie_units.*.model' => 'nullable|string|max:191',
            'walkie_units.*.serial'=> 'nullable|string|max:191',
            'walkie_units.*.unit_id'=> 'nullable|string|max:191',
            'damage_description'   => 'nullable|string',
            'damage_date'          => 'required|date',
            'new_damage_photos'    => 'nullable|array',
            'new_damage_photos.*'  => 'image|mimes:jpeg,jpg,png,webp|max:5120',
            'replacement_amount'   => 'required|numeric|min:0',
            'billing_details'      => 'nullable|string',
            'notes'                => 'nullable|string',
        ]);

        $existingPhotos = $damageReport->damage_photos ?? [];

        // Handle photo deletions
        if ($request->has('delete_photos')) {
            foreach ($request->delete_photos as $path) {
                Storage::disk('public')->delete($path);
                $existingPhotos = array_values(array_filter($existingPhotos, fn($p) => $p !== $path));
            }
        }

        // Upload new photos
        if ($request->hasFile('new_damage_photos')) {
            Storage::disk('public')->makeDirectory('damage-photos');
            foreach ($request->file('new_damage_photos') as $photo) {
                $path = $photo->store('damage-photos', 'public');
                $existingPhotos[] = $path;
            }
        }

        // Clean up walkie_units to remove empty rows
        $walkieUnits = collect($request->walkie_units)->filter(function ($unit) {
            return !empty($unit['model']) || !empty($unit['serial']) || !empty($unit['unit_id']);
        })->values()->toArray();

        $damageReport->update([
            'walkie_units'        => !empty($walkieUnits) ? $walkieUnits : null,
            'damage_description'  => $request->damage_description,
            'damage_date'         => $request->damage_date,
            'damage_photos'       => $existingPhotos ?: null,
            'replacement_amount'  => $request->replacement_amount,
            'billing_details'     => $request->billing_details,
            'notes'               => $request->notes,
        ]);

        // Regenerate PDF
        $this->generatePdf($damageReport);

        return redirect()->route('damage-reports.show', $damageReport)
            ->with('success', 'Damage Report updated and invoice PDF regenerated.');
    }

    // ─── DESTROY ─────────────────────────────────────────────────────────────────
    public function destroy(DamageReport $damageReport)
    {
        // Delete photos from storage
        if ($damageReport->damage_photos) {
            foreach ($damageReport->damage_photos as $photo) {
                Storage::disk('public')->delete($photo);
            }
        }
        // Delete PDF
        if ($damageReport->pdf_path) {
            Storage::disk('public')->delete(str_replace('storage/', '', $damageReport->pdf_path));
        }

        $damageReport->delete();
        return redirect()->route('damage-reports.index')->with('success', 'Damage Report deleted successfully.');
    }

    // ─── GENERATE PDF ────────────────────────────────────────────────────────────
    private function generatePdf(DamageReport $report): void
    {
        $report->load('client', 'subscription');
        $html = view('damage-reports.invoice-pdf', compact('report'))->render();
        $pdf  = PDF::loadHTML($html)->setPaper('a4', 'portrait');

        $fileName = $report->report_code . '.pdf';
        $path     = 'damage-invoices/' . $fileName;
        Storage::disk('public')->makeDirectory('damage-invoices');
        Storage::disk('public')->put($path, $pdf->output());
        $report->update(['pdf_path' => 'storage/' . $path]);
    }

    // ─── SEND (Email / WhatsApp) ─────────────────────────────────────────────────
    public function send(Request $request, DamageReport $damage_report)
    {
        $damageReport = $damage_report;
        $request->validate([
            'method'   => 'required|in:email,whatsapp',
            'to_email' => 'required_if:method,email|email|nullable',
            'cc_emails'=> 'nullable|string',
            'message'  => 'nullable|string',
        ]);

        // Ensure PDF exists
        if (!$damageReport->pdf_path || !Storage::disk('public')->exists(str_replace('storage/', '', $damageReport->pdf_path))) {
            $this->generatePdf($damageReport);
            $damageReport->refresh();
        }

        $hash = substr(md5($damageReport->id . config('app.key')), 0, 8);
        $url  = URL::temporarySignedRoute('damage-reports.download', now()->addDays(30), [
            'hash' => $hash,
            'ref'  => $damageReport->id,
        ]);

        if ($request->method === 'email') {
            $mail = new \App\Mail\DamageInvoiceMail($damageReport, $url, $request->message);

            $mailInstance = \Mail::to($request->to_email);
            if ($request->filled('cc_emails')) {
                $ccEmails = array_filter(array_map('trim', explode(',', $request->cc_emails)));
                if (!empty($ccEmails)) {
                    $mailInstance->cc($ccEmails);
                }
            }
            $mailInstance->send($mail);

            $damageReport->update(['status' => 'sent', 'sent_at' => now()]);
            return back()->with('success', 'Damage invoice sent via email successfully.');
        }

        if ($request->method === 'whatsapp') {
            $customMsg = $request->message
                ? $request->message . "\n\n"
                : "Please find attached the damage/replacement invoice for the walkie-talkie equipment.\n\n";

            $message = "Hello *{$damageReport->client_name}*,\n\n" .
                $customMsg .
                "📄 *Invoice No:* {$damageReport->report_code}\n" .
                "📅 *Damage Date:* {$damageReport->damage_date->format('d M Y')}\n" .
                "🔧 *Damage:* {$damageReport->damage_description}\n" .
                "💰 *Amount:* ₹" . number_format($damageReport->replacement_amount, 2) . "\n\n" .
                "Download Invoice:\n{$url}\n\n" .
                "– *Crewrent Enterprises*";

            $phone = preg_replace('/\D+/', '', $damageReport->client_phone);
            if (strlen($phone) <= 10) $phone = '91' . $phone;

            $damageReport->update(['status' => 'sent', 'sent_at' => now()]);
            return redirect('https://wa.me/' . $phone . '?text=' . rawurlencode($message));
        }

        return back()->with('success', 'Invoice sent successfully.');
    }

    // ─── DOWNLOAD (Signed URL) ───────────────────────────────────────────────────
    public function download($hash, Request $request)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'This download link has expired or is invalid.');
        }

        $reportId     = $request->get('ref');
        $expectedHash = substr(md5($reportId . config('app.key')), 0, 8);

        if ($hash !== $expectedHash) {
            abort(403, 'Invalid link.');
        }

        $report = DamageReport::findOrFail($reportId);

        if (!$report->pdf_path) {
            abort(404, 'Invoice PDF not found.');
        }

        $filePath = storage_path('app/public/' . str_replace('storage/', '', $report->pdf_path));

        if (!file_exists($filePath)) {
            abort(404, 'Invoice PDF file not found on server.');
        }

        return response()->file($filePath, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $report->report_code . '.pdf"',
        ]);
    }

    // ─── MARK PAID ───────────────────────────────────────────────────────────────
    public function markPaid(DamageReport $damage_report)
    {
        $damageReport = $damage_report;
        $damageReport->update(['status' => 'paid', 'paid_at' => now()]);

        PaymentTransaction::create([
            'payable_type'   => DamageReport::class,
            'payable_id'     => $damageReport->id,
            'amount'         => $damageReport->replacement_amount,
            'payment_method' => 'bank_transfer',
            'notes'          => 'Damage replacement charges – ' . $damageReport->report_code,
            'paid_at'        => now(),
            'recorded_by'    => auth()->user()->name ?? 'Admin',
        ]);

        return back()->with('success', 'Damage invoice marked as paid and payment recorded.');
    }

    // ─── REGENERATE PDF ──────────────────────────────────────────────────────────
    public function regeneratePdf(DamageReport $damage_report)
    {
        $this->generatePdf($damage_report);
        return back()->with('success', 'Invoice PDF regenerated successfully.');
    }
}
