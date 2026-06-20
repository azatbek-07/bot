<?php

// .env ni o‘qish
$env = parse_ini_file(__DIR__ . '/.env');

$token = $env['TELEGRAM_BOT_TOKEN'];
$userId = $env['TELEGRAM_USER_ID'];

$message = "Salom 👋";

// Telegram API URL
$url = "https://api.telegram.org/bot{$token}/sendMessage";

// POST data
$data = [
    'chat_id' => $userId,
    'text' => $message
];

// cURL orqali yuborish
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

$response = curl_exec($ch);
curl_close($ch);

// natijani ko‘rish (debug uchun)
echo $response;