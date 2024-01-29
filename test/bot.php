<?php

$data = json_decode(file_get_contents('php://input'), true);

if (!empty($data)) {
    // Перевірка, чи карточка переміщена з колонки "In Progress" в "Done" або навпаки
    if (isset($data['action']['type']) && $data['action']['type'] === 'updateCard' && isset($data['action']['data']['listAfter']) && isset($data['action']['data']['listBefore'])) {
        $cardId = $data['action']['data']['card']['id'];
        $listAfter = $data['action']['data']['listAfter']['name'];
        $listBefore = $data['action']['data']['listBefore']['name'];

        // Ваш код для роботи з отриманою інформацією
        // Наприклад, відправлення повідомлення в Telegram бота
        sendToTelegram("Карточка $cardId переміщена з $listBefore в $listAfter");
    }
}

function sendToTelegram($message) {
    $telegramBotToken = '6326761434:AAEBs-gjnSUzBu3i_k9De6SXW5rbN4M66LY';
    $telegramChatId = '494761070';

    $url = "https://api.telegram.org/bot$telegramBotToken/sendMessage";
    $params = [
        'chat_id' => $telegramChatId,
        'text' => $message,
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}
