# Monthly Subscription - Automatic Email Feature

## Overview
Monthly invoices automatically generate and send via email on billing day at 9:00 AM.

## How It Works

### 1. **CC Emails (Already Working)**
- Send Invoice modal me "CC Emails" field hai
- Multiple emails comma-separated add karo: `email1@example.com, email2@example.com`
- Automatically CC me chale jayenge

### 2. **Automatic Monthly Emails**

#### Command Created:
```bash
php artisan invoices:send-monthly
```

#### What It Does:
1. **Checks Active Subscriptions** - Sirf active subscriptions with today as billing day
2. **Generates Invoice** - Agar invoice exist nahi karta to create karta hai
3. **Generates PDF** - Invoice ka PDF generate karta hai
4. **Sends Email** - Client email pe automatically send karta hai with PDF download link
5. **Updates Status** - Invoice status "sent" mark karta hai

#### Scheduled Time:
- **Daily at 9:00 AM**
- Automatically runs via Laravel Scheduler

#### Requirements:
- Subscription status = 'active'
- Client email must be filled
- Billing day matches today's date

### 3. **Setup Instructions**

#### For Development (Windows with Herd):
```bash
# Run scheduler manually (for testing)
php artisan schedule:run

# Or run command directly
php artisan invoices:send-monthly
```

#### For Production (Linux Server):
Add to crontab:
```bash
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

### 4. **Email Content**
- Professional HTML template
- Invoice details (code, billing period, amount)
- Item types mentioned
- Download link (valid for 30 days)
- Company branding (#004d40 and #ff9800 colors)

### 5. **Testing**
```bash
# Test command manually
php artisan invoices:send-monthly

# Output shows:
# ✓ Sent invoice INV-20250215-1234 to client@example.com
# ✓ Sent invoice INV-20250215-5678 to client2@example.com
# 
# === Summary ===
# Successfully sent: 2
```

### 6. **Error Handling**
- Agar email fail hota hai, error log hota hai
- Other subscriptions continue processing
- Summary me failed count dikhta hai

### 7. **Important Notes**
- Email sirf un subscriptions ke liye jayega jinka:
  - Status = 'active'
  - Client email filled hai
  - Billing day = today
- Duplicate invoices nahi banenge (same period check karta hai)
- PDF automatically generate hota hai agar exist nahi karta

## Manual Sending (Existing Feature)
- Subscription detail page se manually bhi send kar sakte ho
- CC emails add kar sakte ho
- Custom message add kar sakte ho
- WhatsApp option bhi available hai
