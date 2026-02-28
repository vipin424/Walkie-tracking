<?php

return [
    'bank' => [
        'name' => env('BANK_NAME', 'State Bank of India'),
        'account_name' => env('BANK_ACCOUNT_NAME', 'Crewrent Enterprises'),
        'account_number' => env('BANK_ACCOUNT_NUMBER', '1234567890'),
        'ifsc_code' => env('BANK_IFSC_CODE', 'SBIN0001234'),
        'branch' => env('BANK_BRANCH', 'Mumbai Branch'),
        'pan_number' => env('PAN_NUMBER', 'ABCDE1234F'),
    ],
    
    'upi' => [
        'id' => env('UPI_ID', 'crewrent@paytm'),
        'name' => env('UPI_NAME', 'Crewrent Enterprises'),
    ],
];
