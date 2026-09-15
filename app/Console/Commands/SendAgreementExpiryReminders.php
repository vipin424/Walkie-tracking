<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SubscriptionAgreement;
use App\Models\SubscriptionAddendum;
use App\Mail\AgreementExpiryMail;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendAgreementExpiryReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'agreements:expiry-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send 10-day expiry reminders for main agreements and addendums';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $targetDate = Carbon::today()->addDays(10)->toDateString();

        $this->info("Checking for agreements expiring on: $targetDate");

        $emailsSent = 0;

        // 1. Check Main Agreements
        $mainAgreements = SubscriptionAgreement::with('subscription')
            ->where('status', 'signed')
            ->whereDate('agreement_end_date', $targetDate)
            ->get();

        foreach ($mainAgreements as $agreement) {
            $email = $agreement->subscription->client_email;
            if ($email) {
                try {
                    $adminEmail = config('mail.admin_email') ?? env('ADMIN_EMAIL', 'info@crewrent.in');
                    Mail::to($email)
                        ->cc($adminEmail ? [$adminEmail] : [])
                        ->send(new AgreementExpiryMail('main', $agreement));
                    $emailsSent++;
                    $this->line("Sent main expiry to $email for {$agreement->agreement_code}");
                } catch (\Exception $e) {
                    Log::error("Failed to send expiry mail for main agreement {$agreement->agreement_code}: " . $e->getMessage());
                }
            }
        }

        // 2. Check Addendums
        $addendums = SubscriptionAddendum::with('subscription')
            ->where('status', 'signed')
            ->whereDate('agreement_end_date', $targetDate)
            ->get();

        foreach ($addendums as $addendum) {
            $email = $addendum->subscription->client_email;
            if ($email) {
                try {
                    $adminEmail = config('mail.admin_email') ?? env('ADMIN_EMAIL', 'info@crewrent.in');
                    Mail::to($email)
                        ->cc($adminEmail ? [$adminEmail] : [])
                        ->send(new AgreementExpiryMail('addendum', $addendum));
                    $emailsSent++;
                    $this->line("Sent addendum expiry to $email for {$addendum->addendum_code}");
                } catch (\Exception $e) {
                    Log::error("Failed to send expiry mail for addendum {$addendum->addendum_code}: " . $e->getMessage());
                }
            }
        }

        $this->info("Done! Total emails sent: $emailsSent");
    }
}
