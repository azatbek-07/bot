<?php
// Xabar yuborilganini tekshirish
$response = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['message'])) {
    // .env ni o'qish
    $env = parse_ini_file(__DIR__ . '/.env');

    $token = $env['TELEGRAM_BOT_TOKEN'];
    $userId = $env['TELEGRAM_USER_ID'];

    // Textarea dan kelgan xabarni olish
    $message = trim($_POST['message']);

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

    // JSON natijani decode qilish
    $result = json_decode($response, true);
}
?>

<!DOCTYPE html>
<html lang="uz">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Telegram Bot - Xabar yuborish</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #0088cc;
            text-align: center;
        }

        textarea {
            width: 100%;
            min-height: 150px;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            resize: vertical;
            box-sizing: border-box;
        }

        textarea:focus {
            outline: none;
            border-color: #0088cc;
        }

        button {
            background: #0088cc;
            color: white;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            width: 100%;
        }

        button:hover {
            background: #006699;
        }

        .message {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>📨 Telegram Bot</h1>

        <form method="POST" action="">
            <label for="message"><strong>Xabaringizni yozing:</strong></label>
            <textarea
                id="message"
                name="message"
                placeholder="Bu yerga xabaringizni yozing..."
                required></textarea>

            <button type="submit">🚀 Xabarni yuborish</button>
        </form>

        <?php if ($response): ?>
            <?php if ($result && $result['ok']): ?>
                <div class="message success">
                    ✅ Xabar muvaffaqiyatli yuborildi!
                </div>
            <?php else: ?>
                <div class="message error">
                    ❌ Xatolik yuz berdi: <?php echo htmlspecialchars($response); ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</body>

</html>