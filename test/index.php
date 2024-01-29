<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = file_get_contents('php://input');
    error_log($payload);  // Додаємо в лог вхідні дані
    $payloadData = json_decode($payload, true);

    if (
        isset($payloadData['action']['type']) &&
        isset($payloadData['action']['data']['listBefore']['name']) &&
        isset($payloadData['action']['data']['listAfter']['name']) &&
        isset($payloadData['action']['data']['card']['name'])
    ) {
        $actionType = $payloadData['action']['type'];
        $listBefore = $payloadData['action']['data']['listBefore']['name'];
        $listAfter = $payloadData['action']['data']['listAfter']['name'];

        $isMovedToDone = $listBefore === 'In Progress' && $listAfter === 'Done';
        $isMovedToInProgress = $listBefore === 'Done' && $listAfter === 'In Progress';

        if ($actionType === 'updateCard' && ($isMovedToDone || $isMovedToInProgress)) {
            $cardName = $payloadData['action']['data']['card']['name'];
            $telegramBotToken = '6326761434:AAEBs-gjnSUzBu3i_k9De6SXW5rbN4M66LY';
            $chatId = '494761070';
            $message = "Card '$cardName' moved from '$listBefore' to '$listAfter'";

            $telegramUrl = "https://api.telegram.org/bot{$telegramBotToken}/sendMessage?chat_id={$chatId}&text={$message}";
            $telegramResponse = file_get_contents($telegramUrl);

            // Додаємо в лог результат відправки в Telegram
            error_log("Telegram Response: $telegramResponse");

            // Виводимо результат відправки в Telegram у відповідь
            echo $telegramResponse;
        }
    }
}