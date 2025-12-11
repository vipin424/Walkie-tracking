<?php

namespace App\Services;

class WhatsAppService
{
    public static function send($phone, $message)
    {
        $apiKey = env('WHATSAPP_API_KEY');

        $url = "https://api.callmebot.com/whatsapp.php?phone={$phone}&text=" . urlencode($message) . "&apikey={$apiKey}";

        $response = file_get_contents($url);

        return $response ? true : false;
    }
}
