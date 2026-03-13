# Monthly Subscription Module - Setup Guide

## Overview
Yeh module monthly recurring billing ke liye hai. Jo clients walkie-talkies monthly basis pe lete hain, unke liye automatic invoice generation aur WhatsApp/Email delivery.

## Features
✅ Monthly subscription management
✅ Automatic invoice generation with billing period (e.g., 13 March to 13 April)
✅ Existing PDF template use karta hai
✅ WhatsApp aur Email se invoice send kar sakte ho
✅ Payment tracking (Pending/Sent/Paid)
✅ Subscription status management (Active/Paused/Cancelled)

## Installation Steps

### 1. Database Migration Run Karo
```bash
php artisan migrate
```

### 2. Storage Link (Agar pehle se nahi hai)
```bash
php artisan storage:link
```

### 3. Cron Job Setup (Optional - Auto Invoice Generation)
Agar aap chahte ho ki har mahine automatically invoice generate ho, to server pe cron job setup karo:

**Linux/Mac:**
```bash
crontab -e
```
Add this line:
```
0 0 * * * cd /path/to/your/project && php artisan invoices:generate-monthly
```

**Windows (Task Scheduler):**
- Task Scheduler open karo
- Create Basic Task
- Trigger: Daily
- Action: Start a program
- Program: `C:\path\to\php.exe`
- Arguments: `artisan invoices:generate-monthly`
- Start in: `C:\path\to\your\project`

### 4. Manual Invoice Generation Command
```bash
php artisan invoices:generate-monthly
```

## Usage Guide

### 1. New Monthly Subscription Create Karna
1. Sidebar se "Monthly Subscriptions" pe click karo
2. "New Subscription" button click karo
3. Client details fill karo:
   - Client Name, Phone, Email
   - Billing Start Date (jis din se billing shuru hogi)
   - Billing Day of Month (1-28, e.g., 13 for 13th of every month)
   - Monthly Amount
4. Items add karo (walkie-talkie models, quantities, rates)
5. Notes add karo (terms & conditions)
6. "Create Subscription" click karo

### 2. Invoice Generate Karna
**Manual:**
1. Subscription detail page pe jao
2. "Generate Invoice" button click karo
3. Current billing period ka invoice automatically ban jayega

**Automatic:**
- Cron job daily check karega
- Jis din billing day hai, us din automatically invoice generate hoga

### 3. Invoice Send Karna
1. Subscription detail page pe generated invoices list dikhegi
2. "Send" button (WhatsApp icon) click karo
3. WhatsApp web open hoga with pre-filled message
4. Message send karo

### 4. Payment Mark Karna
1. Jab client payment kar de
2. Invoice ke saamne "Mark Paid" button click karo
3. Status "Paid" ho jayega

### 5. Subscription Edit/Pause/Cancel
1. Subscription detail page pe "Edit" click karo
2. Details update karo
3. Status change kar sakte ho:
   - **Active**: Invoice generate hote rahenge
   - **Paused**: Temporarily stop (invoice nahi banenge)
   - **Cancelled**: Permanently stop

## Invoice PDF Format
Invoice PDF mein automatically yeh details hongi:
- Company logo aur details
- Client information
- **Billing Period**: "13 March 2026 to 13 April 2026" (example)
- Items table with quantities and rates
- Total amount
- Notes/Terms

## Database Tables

### monthly_subscriptions
- subscription_code (unique)
- client details (name, email, phone)
- billing_start_date
- billing_day_of_month (1-28)
- monthly_amount
- items_json (array of items)
- status (active/paused/cancelled)

### monthly_invoices
- invoice_code (unique)
- subscription_id
- billing_period_from
- billing_period_to
- amount
- pdf_path
- status (pending/sent/paid)
- sent_at, paid_at timestamps

## Routes
```
GET  /subscriptions              - List all subscriptions
GET  /subscriptions/create       - Create new subscription form
POST /subscriptions              - Store new subscription
GET  /subscriptions/{id}         - View subscription details
GET  /subscriptions/{id}/edit    - Edit subscription form
PUT  /subscriptions/{id}         - Update subscription
GET  /subscriptions/{id}/generate-invoice - Generate invoice manually
POST /monthly-invoice/{id}/send  - Send invoice via WhatsApp/Email
POST /monthly-invoice/{id}/mark-paid - Mark invoice as paid
GET  /monthly-invoice/{hash}/download - Download invoice PDF (signed URL)
```

## Example Workflow

### Scenario: Client "ABC Company" monthly walkie-talkies leta hai
1. **Day 1**: Subscription create karo
   - Billing Day: 13
   - Monthly Amount: ₹15,000
   - Items: 10x Walkie Model A @ ₹1,500/month

2. **Day 13 (Every Month)**: 
   - Cron job automatically invoice generate karega
   - Billing Period: 13 March to 13 April
   
3. **Manual Send**:
   - Dashboard se invoice send karo WhatsApp pe
   - Client ko PDF link milega (30 days valid)

4. **Payment Receive**:
   - Client payment kare
   - "Mark Paid" click karo

5. **Next Month**:
   - 13 April ko automatically next invoice generate hoga
   - Billing Period: 13 April to 13 May

## Troubleshooting

### Invoice PDF nahi ban raha
```bash
# Storage permissions check karo
chmod -R 775 storage/app/public
php artisan storage:link
```

### Cron job kaam nahi kar raha
```bash
# Manually test karo
php artisan invoices:generate-monthly

# Laravel scheduler check karo
php artisan schedule:list
```

### WhatsApp link nahi khul raha
- Browser mein WhatsApp Web login hona chahiye
- Phone number format check karo (91 prefix automatically add hota hai)

## Support
Koi issue ho to check karo:
- `storage/logs/laravel.log` - Error logs
- Database migrations properly run hue ya nahi
- Routes properly registered hain ya nahi (`php artisan route:list`)
