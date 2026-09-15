<?php

namespace App\Services;

use Carbon\Carbon;

class SubscriptionBillingService
{
    /**
     * Fixed days per month for pro-rate calculation (as per business rule).
     */
    const DAYS_IN_MONTH = 30;

    /**
     * Check if today is in the mid-cycle window.
     * Mid-cycle = today is BEFORE the billing day of the current month.
     * (i.e., billing hasn't happened yet this month)
     */
    public function isMidCycle(int $billingDay): bool
    {
        return Carbon::today()->day < $billingDay;
    }

    /**
     * Get the next upcoming billing date based on billing day.
     * If today < billing_day  → billing date is this month's billing_day
     * If today >= billing_day → billing date is next month's billing_day
     */
    public function getNextBillingDate(int $billingDay): Carbon
    {
        $today = Carbon::today();

        if ($today->day < $billingDay) {
            return Carbon::create($today->year, $today->month, $billingDay);
        }

        // Already past billing day this month → next month
        return Carbon::create($today->year, $today->month, $billingDay)->addMonth();
    }

    /**
     * Calculate how many days remain from a given date until the next billing date (inclusive of from, exclusive of billing).
     * Formula: billing_date - added_on (in days)
     */
    public function getRemainingDays(Carbon $addedOn, Carbon $nextBillingDate): int
    {
        // days from addedOn up to (but not including) billing date
        $days = $addedOn->diffInDays($nextBillingDate);
        return max(1, (int) $days);
    }

    /**
     * Calculate pro-rated amount for a mid-cycle item.
     * pro_rated = rate * quantity * (remaining_days / 30)
     */
    public function calculateProRatedAmount(float $rate, int $quantity, int $remainingDays): float
    {
        return round($rate * $quantity * ($remainingDays / self::DAYS_IN_MONTH), 2);
    }

    /**
     * Process submitted items array during an edit/update.
     * Compare existing items with new items to detect newly added ones.
     * If mid-cycle, mark new items with is_mid_cycle = true and metadata.
     *
     * @param array $existingItems   Current items_json from DB
     * @param array $submittedItems  Items submitted from the form
     * @param int   $billingDay      billing_day_of_month
     * @return array                 Processed items array ready to save
     */
    public function processItemsOnUpdate(array $existingItems, array $submittedItems, int $billingDay): array
    {
        $today           = Carbon::today();
        $midCycle        = $this->isMidCycle($billingDay);
        $nextBillingDate = $this->getNextBillingDate($billingDay);

        // Build a lookup of existing item names (for quick comparison)
        $existingNames = collect($existingItems)->pluck('name')->map(fn($n) => strtolower(trim($n)))->toArray();

        $processed = [];

        foreach ($submittedItems as $item) {
            $itemName = strtolower(trim($item['name'] ?? ''));

            // Preserve existing mid-cycle metadata if item was already mid-cycle and not yet billed
            if (isset($item['is_mid_cycle']) && $item['is_mid_cycle'] == '1') {
                // Already marked mid-cycle from a previous save — keep it
                $item['is_mid_cycle']      = true;
                $item['added_on']          = $item['added_on'] ?? $today->toDateString();
                $item['pro_rated_until']   = $item['pro_rated_until'] ?? $nextBillingDate->toDateString();
            } elseif ($midCycle && (!empty($item['is_new_row']) || !in_array($itemName, $existingNames))) {
                // Newly added item during mid-cycle window
                $item['is_mid_cycle']    = true;
                $item['added_on']        = $today->toDateString();
                $item['pro_rated_until'] = $nextBillingDate->toDateString();
            } else {
                // Existing item or item added after billing day — no pro-rate
                $item['is_mid_cycle']    = false;
                $item['added_on']        = $item['added_on'] ?? null;
                $item['pro_rated_until'] = null;
            }

            $processed[] = $item;
        }

        return $processed;
    }

    /**
     * Calculate the total invoice amount considering mid-cycle pro-rated items.
     * Returns an array with:
     *  - 'total'         => total invoice amount
     *  - 'items_detail'  => per-item breakdown with computed amount and pro-rate info
     */
    public function calculateInvoiceAmounts(array $items): array
    {
        $total       = 0;
        $itemDetails = [];

        foreach ($items as $item) {
            $rate     = (float) ($item['rate'] ?? 0);
            $quantity = (int)   ($item['quantity'] ?? 1);
            $isMid    = !empty($item['is_mid_cycle']);

            if ($isMid && !empty($item['added_on']) && !empty($item['pro_rated_until'])) {
                $addedOn    = Carbon::parse($item['added_on']);
                $billingEnd = Carbon::parse($item['pro_rated_until']);
                $days       = $this->getRemainingDays($addedOn, $billingEnd);
                $amount     = $this->calculateProRatedAmount($rate, $quantity, $days);

                $itemDetails[] = array_merge($item, [
                    'computed_amount'    => $amount,
                    'is_pro_rated'       => true,
                    'pro_rated_days'     => $days,
                    'pro_rated_label'    => $addedOn->format('d M') . ' – ' . $billingEnd->format('d M') . ' (' . $days . ' days)',
                ]);
            } else {
                $amount = round($rate * $quantity, 2);
                $itemDetails[] = array_merge($item, [
                    'computed_amount' => $amount,
                    'is_pro_rated'    => false,
                ]);
            }

            $total += $amount;
        }

        return [
            'total'        => round($total, 2),
            'items_detail' => $itemDetails,
        ];
    }

    /**
     * After invoice is generated for the current billing cycle,
     * clear mid-cycle flags from items so next month they bill at full rate.
     */
    public function clearMidCycleFlags(array $items): array
    {
        return array_map(function ($item) {
            $item['is_mid_cycle']    = false;
            $item['added_on']        = null;
            $item['pro_rated_until'] = null;
            return $item;
        }, $items);
    }

    /**
     * For new subscriptions (store), mark all items as non-mid-cycle.
     */
    public function processItemsOnCreate(array $submittedItems): array
    {
        return array_map(function ($item) {
            $item['is_mid_cycle']    = false;
            $item['added_on']        = null;
            $item['pro_rated_until'] = null;
            return $item;
        }, $submittedItems);
    }
}
