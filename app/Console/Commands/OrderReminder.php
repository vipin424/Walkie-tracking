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

        $targetDate = now()->addDays(3)->toDateString();
        $orders = Order::with('client')
            ->where('event_from', $targetDate)
            ->where('status', '!=', 'dispatched')
            ->get();

        foreach ($orders as $order) {
            Mail::to('reelrententerprises@gmail.com')
                ->send(new OrderReminderMail($order));
        }

        $this->info('Order reminders sent successfully.');

    }
}
