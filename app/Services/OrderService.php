<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Client;
use App\Models\OrderItem;
use App\Models\PaymentTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    /**
     * Calculates the totals for an order based on items, dates, and extra charges.
     */
    public function calculateTotals(array $data)
    {
        $eventFrom = Carbon::parse($data['event_from']);
        $eventTo   = Carbon::parse($data['event_to']);
        $totalDays = $eventFrom->diffInDays($eventTo) + 1;

        $subtotal   = 0;
        $tax_amount = 0;
        $discount   = floatval($data['discount_amount'] ?? 0);

        foreach ($data['items'] as $row) {
            $qty  = floatval($row['quantity']);
            $unit = floatval($row['unit_price']);
            $taxp = floatval($row['tax_percent'] ?? 0);

            $baseTotal = $qty * $unit * $totalDays;
            $lineTax   = $baseTotal * ($taxp / 100);

            $subtotal   += $baseTotal;
            $tax_amount += $lineTax;
        }

        $extraChargeType = $data['extra_charge_type'] ?? null;
        $extraRate  = floatval($data['extra_charge_rate'] ?? 0);
        $staffCount = intval($data['staff_count'] ?? 1);
        $extraTotal = 0;

        if ($extraChargeType === 'delivery') {
            $extraTotal = floatval($data['delivery_charge_amount'] ?? 0);
        } elseif ($extraChargeType === 'staff') {
            $extraTotal = $extraRate * $totalDays * $staffCount;
        }

        $travellingCharge = floatval($data['travelling_charge_amount'] ?? 0);
        $total = $subtotal + $tax_amount + $extraTotal + $travellingCharge - $discount;

        return [
            'total_days'         => $totalDays,
            'subtotal'           => $subtotal,
            'tax_amount'         => $tax_amount,
            'extra_charge_type'  => $extraChargeType,
            'extra_charge_rate'  => $extraRate,
            'staff_count'        => $staffCount,
            'extra_charge_total' => $extraTotal,
            'travelling_charge'  => $travellingCharge,
            'discount_amount'    => $discount,
            'total_amount'       => $total,
        ];
    }

    /**
     * Resolves or creates a client based on the provided data.
     */
    public function resolveClient(array $data)
    {
        $client = Client::firstOrCreate(
            ['contact_number' => $data['client_phone']],
            [
                'name' => $data['client_name'],
                'email' => $data['client_email'] ?? null,
            ]
        );

        return $client;
    }

    /**
     * Creates a new order.
     */
    public function createOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            $client = $this->resolveClient($data);
            $totals = $this->calculateTotals($data);

            $advancePaid = floatval($data['advance_paid'] ?? 0);
            $balance = $totals['total_amount'] - $advancePaid;

            $order = Order::create([
                'order_code' => Order::generateCode(),
                'client_id'  => $client->id,
                'client_name'  => $data['client_name'],
                'client_email' => $data['client_email'] ?? null,
                'client_phone' => $data['client_phone'],
                
                'event_from' => $data['event_from'],
                'event_to'   => $data['event_to'],
                'event_time' => $data['event_time'] ?? null,
                'event_location' => $data['event_location'] ?? null,
                'handle_type' => $data['handle_type'] === 'self' ? 1 : 0,
                'notes'      => $data['notes'] ?? null,
                'bill_to'    => $data['bill_to'] ?? null,
                
                'total_days' => $totals['total_days'],
                'subtotal' => $totals['subtotal'],
                'tax_amount' => $totals['tax_amount'],
                'extra_charge_type'  => $totals['extra_charge_type'],
                'extra_charge_rate'  => $totals['extra_charge_rate'],
                'staff_count'        => $totals['staff_count'],
                'extra_charge_total' => $totals['extra_charge_total'],
                'travelling_charge'  => $totals['travelling_charge'],
                'discount_amount' => $totals['discount_amount'],
                'total_amount' => $totals['total_amount'],
                
                'security_deposit' => floatval($data['security_deposit'] ?? 0),
                'advance_paid'  => $advancePaid,
                'balance_amount'=> $balance,
                'final_payable' => $balance,
                'agreement_required' => $data['handle_type'] === 'self',
                'payment_status' => $advancePaid >= $totals['total_amount'] ? 'paid' : ($advancePaid > 0 ? 'partial' : 'pending'),
                
                'status' => 'confirmed',
                'created_by' => Auth::id() ?? 1, // Fallback for seeds/commands
            ]);

            $this->saveOrderItems($order, $data['items'], $totals['total_days']);

            if ($advancePaid > 0) {
                $this->recordAdvancePayment($order, $advancePaid);
            }

            return $order;
        });
    }

    /**
     * Updates an existing order.
     */
    public function updateOrder(Order $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            $client = $this->resolveClient($data);
            $totals = $this->calculateTotals($data);

            $advancePaid = floatval($data['advance_paid'] ?? 0);
            $balance = $totals['total_amount'] - $advancePaid;

            $order->update([
                'client_id'  => $client->id,
                'client_name'  => $data['client_name'],
                'client_email' => $data['client_email'] ?? null,
                'client_phone' => $data['client_phone'],
                
                'event_from' => $data['event_from'],
                'event_to'   => $data['event_to'],
                'event_time' => $data['event_time'] ?? null,
                'event_location' => $data['event_location'] ?? null,
                'handle_type' => $data['handle_type'] === 'self' ? 1 : 0,
                'notes'      => $data['notes'] ?? null,
                'bill_to'    => $data['bill_to'] ?? null,
                
                'total_days' => $totals['total_days'],
                'subtotal' => $totals['subtotal'],
                'tax_amount' => $totals['tax_amount'],
                'extra_charge_type'  => $totals['extra_charge_type'],
                'extra_charge_rate'  => $totals['extra_charge_rate'],
                'staff_count'        => $totals['staff_count'],
                'extra_charge_total' => $totals['extra_charge_total'],
                'travelling_charge'  => $totals['travelling_charge'],
                'discount_amount' => $totals['discount_amount'],
                'total_amount' => $totals['total_amount'],
                
                'security_deposit' => floatval($data['security_deposit'] ?? 0),
                'advance_paid'  => $advancePaid,
                'balance_amount'=> $balance,
                'final_payable' => $balance,
                'agreement_required' => $data['handle_type'] === 'self',
                'payment_status' => $advancePaid >= $totals['total_amount'] ? 'paid' : ($advancePaid > 0 ? 'partial' : 'pending'),
            ]);

            // Replace Items
            $order->items()->delete();
            $this->saveOrderItems($order, $data['items'], $totals['total_days']);

            $this->syncAdvancePayment($order, $advancePaid);

            return $order;
        });
    }

    /**
     * Completes/Settles an order calculating damages and late fees.
     */
    public function settleOrder(Order $order, array $data)
    {
        return DB::transaction(function () use ($order, $data) {
            $damage     = floatval($data['damage_charge'] ?? 0);
            $late       = floatval($data['late_fee'] ?? 0);
            $travelling = floatval($data['settlement_travelling_charges'] ?? 0);
            $food       = floatval($data['settlement_food_charges'] ?? 0);

            $deposit = floatval($order->security_deposit);
            $balance = floatval($order->balance_amount);
            $advance = floatval($order->advance_paid);
            $total   = floatval($order->total_amount);

            $extraCharges = $travelling + $food;
            $depositRemaining = $deposit - ($damage + $late);
            $depositUsedForBalance = 0;

            if ($depositRemaining >= 0) {
                // deposit covers damages
                $finalPayable = $balance + $extraCharges - $depositRemaining;

                if ($finalPayable <= 0) {
                    $refund = abs($finalPayable);
                    $finalPayable = 0;
                    $depositUsedForBalance = $balance + $extraCharges;
                } else {
                    $refund = 0;
                    $depositUsedForBalance = $depositRemaining;
                }
            } else {
                // deposit not enough
                $finalPayable = $balance + $extraCharges + abs($depositRemaining);
                $refund = 0;
                $depositUsedForBalance = 0;
            }
     
            $order->update([    
                'damage_charge'                 => $damage,
                'late_fee'                      => $late,
                'settlement_travelling_charges' => $travelling,
                'settlement_food_charges'       => $food,
                'deposit_adjusted'              => max($depositRemaining, 0),
                'refund_amount'                 => $refund,
                'final_payable'                 => $finalPayable,
                'settlement_status'             => 'settled',
                'settlement_date'               => now(),
                'payment_status'                => $finalPayable > 0 ? 'partial' : 'paid',
            ]);

            // Record payment transaction if deposit was used for balance
            if ($depositUsedForBalance > 0) {
                PaymentTransaction::create([
                    'order_id' => $order->id,
                    'payable_type' => Order::class,
                    'payable_id' => $order->id,
                    'amount' => $depositUsedForBalance,
                    'payment_method' => 'other',
                    'transaction_id' => null,
                    'notes' => 'Security deposit adjusted against remaining balance during settlement',
                    'paid_at' => now(),
                    'recorded_by' => Auth::user()->name ?? 'Admin',
                ]);
            } elseif ($advance >= $total && $finalPayable <= 0 && $balance > 0) {
                // Handle edge case where full payment was in advance, and balance is purely academic. (Based on original logic)
                PaymentTransaction::create([
                    'order_id' => $order->id,
                    'payable_type' => Order::class,
                    'payable_id' => $order->id,
                    'amount' => $advance,
                    'payment_method' => 'bank_transfer',
                    'transaction_id' => null,
                    'notes' => 'Full advance payment recorded during settlement',
                    'paid_at' => now(),
                    'recorded_by' => Auth::user()->name ?? 'Admin',
                ]);
            }

            return $order;
        });
    }

    private function saveOrderItems(Order $order, array $items, int $totalDays)
    {
        foreach ($items as $row) {
            $qty  = intval($row['quantity']);
            $unit = floatval($row['unit_price']);
            $taxp = floatval($row['tax_percent'] ?? 0);

            $baseTotal = $qty * $unit * $totalDays;
            $lineTax   = $baseTotal * ($taxp / 100);

            $order->items()->create([
                'item_name'   => $row['item_name'],
                'item_type'   => $row['item_type'] ?? null,
                'description' => $row['description'] ?? null,
                'quantity'    => $qty,
                'unit_price'  => $unit,
                'tax_percent' => $taxp,
                'total_price' => $baseTotal + $lineTax,
            ]);
        }
    }

    private function recordAdvancePayment(Order $order, float $advancePaid)
    {
        PaymentTransaction::create([
            'order_id'       => $order->id,
            'payable_type'   => Order::class,
            'payable_id'     => $order->id,
            'amount'         => $advancePaid,
            'payment_method' => 'gpay', // Hardcoded as per original, can be dynamic later
            'transaction_id' => null,
            'notes'          => 'Advance payment received during order creation',
            'paid_at'        => now(),
            'recorded_by'    => Auth::user()->name ?? 'Admin',
        ]);
    }

    private function syncAdvancePayment(Order $order, float $advancePaid)
    {
        $advanceTransaction = PaymentTransaction::where('order_id', $order->id)
            ->where('notes', 'Advance payment received during order creation')
            ->first();

        if ($advancePaid > 0) {
            if ($advanceTransaction) {
                $advanceTransaction->update(['amount' => $advancePaid]);
            } else {
                $this->recordAdvancePayment($order, $advancePaid);
            }
        } else {
            if ($advanceTransaction) {
                $advanceTransaction->delete();
            }
        }
    }
}
