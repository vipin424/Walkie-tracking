# Payment Reminder System - Monthly Subscriptions

## Overview
Automatic payment reminders are sent for unpaid invoices 7 days after the invoice was sent.

## How It Works

### 1. **Trigger Conditions**
Payment reminder is sent when:
- Invoice status = 'pending' or 'sent' (not paid)
- Invoice was sent 7+ days ago
- Subscription status = 'active'
- Client email is available

### 2. **Command**
```bash
php artisan invoices:send-reminders
```

### 3. **Schedule**
- **Daily at 10:00 AM**
- Runs automatically via Laravel Scheduler
- Checks all unpaid invoices

### 4. **Email Content**

#### Reminder Message (Highlighted):
```
⚠️ Payment Reminder

This is a friendly reminder that your invoice is still pending payment. 
Please process the payment at your earliest convenience.
```

#### Features:
- Yellow warning box with reminder message
- All invoice details (code, period, amount)
- Download link (valid for 30 days)
- CC emails included (if configured)

### 5. **Smart Features**

#### Prevents Duplicate Reminders:
- Only one reminder per day per invoice
- Uses `updated_at` timestamp to track
- Won't spam clients with multiple emails

#### Example Timeline:
```
Day 1 (15 Feb): Invoice sent automatically
Day 8 (22 Feb): Payment reminder sent (7 days later)
Day 9 (23 Feb): No reminder (already sent yesterday)
Day 15 (1 Mar): Reminder sent again (if still unpaid)
```

### 6. **Testing**
```bash
# Test command manually
php artisan invoices:send-reminders

# Output:
# ✓ Sent reminder for INV-20250215-1234 to client@example.com
# ⊘ Skipped INV-20250215-5678 - Reminder already sent today
# 
# === Payment Reminder Summary ===
# Reminders sent: 1
```

### 7. **Email Template**
- Reminder message in yellow warning box
- ⚠️ icon for attention
- Professional and polite tone
- All invoice details included
- Download button for PDF

### 8. **Automatic Schedule**
```
09:00 AM - Send new monthly invoices (billing day)
10:00 AM - Send payment reminders (7+ days unpaid)
```

### 9. **Stop Reminders**
Reminders automatically stop when:
- Invoice is marked as 'paid'
- Subscription is paused/cancelled
- Client email is removed

### 10. **Manual Override**
You can still manually send invoices from the subscription detail page:
- Custom message option available
- CC emails can be added
- WhatsApp option available

## Benefits
- ✅ Automatic follow-up for unpaid invoices
- ✅ Reduces manual work
- ✅ Improves cash flow
- ✅ Professional reminder system
- ✅ No duplicate reminders
- ✅ CC emails included automatically
