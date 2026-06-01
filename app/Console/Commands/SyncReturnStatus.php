<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class SyncReturnStatus extends Command
{
    protected $signature   = 'orders:sync-return-status';
    protected $description = 'Sync return_status for pre-existing orders that were completed/settled before return tracking was added.';

    public function handle(): int
    {
        $this->info('Syncing return status for existing orders...');

        // 1. All SETTLED orders → fully_returned
        //    (settlement done = items were returned)
        $settled = DB::table('orders')
            ->where('settlement_status', 'settled')
            ->where('return_status', 'pending')
            ->update(['return_status' => 'fully_returned']);

        $this->line("  ✅  Settled orders marked fully_returned : <info>{$settled}</info>");

        // 2. Orders whose event has already ended (event_to < today)
        //    AND have no return_items recorded (i.e., created before tracking)
        //    → mark as fully_returned (they pre-date tracking)
        $pastOrders = DB::table('orders')
            ->where('return_status', 'pending')
            ->whereDate('event_to', '<', now()->startOfDay())
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                  ->from('order_return_items')
                  ->whereColumn('order_return_items.order_id', 'orders.id');
            })
            ->update(['return_status' => 'fully_returned']);

        $this->line("  ✅  Past orders (no return records) marked fully_returned : <info>{$pastOrders}</info>");

        // 3. Orders that are NOT yet settled but event hasn't ended yet → stay as pending
        $stillPending = DB::table('orders')
            ->where('return_status', 'pending')
            ->count();

        $this->line("  ⏳  Orders still pending return : <info>{$stillPending}</info>");

        $this->newLine();
        $this->info('Done! Return status sync complete.');

        return self::SUCCESS;
    }
}
