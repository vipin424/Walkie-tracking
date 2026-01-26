<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\OrderReminderMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OrderReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:order-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Order reminder emails for orders starting in 2 days';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = now()->startOfDay();
        $threeDaysLater = now()->addDays(3)->endOfDay();

        $orders = Order::with('client')
            ->whereDate('event_from', '>=', $today)
            ->whereDate('event_from', '<=', $threeDaysLater)
            ->where('status', '!=', 'dispatched')
            ->get();

        foreach ($orders as $order) {

            // 🔥 Calculate days left
            $eventDate = Carbon::parse($order->event_from)->startOfDay();
            $daysLeft = $today->diffInDays($eventDate, false);

            // 🔥 Text based on days
            if ((int) $daysLeft === 0) {
                $reminderText = 'Today';
            } elseif ((int) $daysLeft === 1) {
                $reminderText = 'Tomorrow';
            } else {
                $reminderText = $daysLeft . ' days';
            }

            Mail::to('reelrententerprises@gmail.com')
                ->send(new OrderReminderMail($order, $reminderText));
        }

        $this->info('Daily order reminders sent successfully.');
    }
}
