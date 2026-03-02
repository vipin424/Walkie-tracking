# Payment Details Setup Guide

## Overview
Jab order ka settlement status "settled" ho aur payment due ho, to PDF invoice mein automatically:
- Bank account details display honge
- UPI QR code generate hoga with payment amount
- Customer QR code scan karke directly payment kar sakta hai

## Setup Instructions

### 1. .env File Configuration
Apne `.env` file mein ye details add karein (already added):

```env
# Bank Details for Invoice
BANK_NAME="State Bank of India"
BANK_ACCOUNT_NAME="Crewrent Enterprises"
BANK_ACCOUNT_NUMBER="1234567890"
BANK_IFSC_CODE="SBIN0001234"
BANK_BRANCH="Mumbai Branch"

# UPI Details
UPI_ID="crewrent@paytm"
UPI_NAME="Crewrent Enterprises"
```

### 2. Apni Details Update Karein
`.env` file mein apni actual bank aur UPI details dalein:
- `BANK_NAME` - Apna bank ka naam
- `BANK_ACCOUNT_NAME` - Account holder ka naam
- `BANK_ACCOUNT_NUMBER` - Apna account number
- `BANK_IFSC_CODE` - Bank ka IFSC code
- `BANK_BRANCH` - Branch ka naam
- `UPI_ID` - Apni UPI ID (e.g., yourname@paytm, yourname@ybl)
- `UPI_NAME` - UPI account holder ka naam

### 3. Cache Clear Karein
Configuration update karne ke baad cache clear karein:

```bash
php artisan config:clear
php artisan cache:clear
```

## Features

### 1. Automatic QR Code Generation
- QR code automatically generate hota hai with exact payment amount
- Customer scan karte hi UPI app mein amount pre-filled aata hai
- Order code automatically payment note mein add hota hai

### 2. Bank Details Display
- Complete bank details PDF mein display hote hain
- Customer bank transfer bhi kar sakta hai

### 3. Conditional Display
Payment details sirf tab show hote hain jab:
- Settlement status = "settled"
- Final payable amount > 0

### 4. Security
- Bank details directly code mein nahi hain
- Sab details `.env` file mein securely stored hain
- `.env` file git mein commit nahi hoti

## UPI QR Code Format
QR code mein ye information hoti hai:
- UPI ID
- Payee Name
- Amount (exact payment amount)
- Currency (INR)
- Transaction Note (Order code)

## Testing
1. Koi order create karein
2. Settlement status "settled" karein with final_payable > 0
3. PDF generate karein
4. Check karein ki bank details aur QR code display ho rahe hain
5. QR code scan karke test karein (optional)

## Notes
- QR code Google Charts API use karta hai (free service)
- Internet connection chahiye QR code generate karne ke liye
- Agar QR code load nahi ho to internet connection check karein
