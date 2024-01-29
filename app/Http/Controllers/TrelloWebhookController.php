<?php

namespace App\Http\Controllers;

use App\Models\TelegramUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TrelloWebhookController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $data = json_decode($request->getContent(), true);

        // Перевірка, чи є зміни на дошці
        if ($this->shouldProcessWebhook($data)) {
            // Перевірка, чи карточка переміщена з колонки "In Progress" в "Done" або навпаки
            if ($this->isCardMoved($data)) {
                // Отримайте інформацію про переміщення карточки
                $cardId = $data['action']['data']['card']['id'];
                $listAfter = $data['action']['data']['listAfter']['name'];
                $listBefore = $data['action']['data']['listBefore']['name'];

                // Ваш код для відправлення повідомлення в Telegram
                $this->sendTelegramMessage("Карточка $cardId переміщена з $listBefore в $listAfter");

                // Пошук та збереження інформації про користувача, який викликав команду "/start"
                $this->saveUserFromStartCommand($data);
            }
        }

        return response()->json(['success' => true]);
    }

    private function shouldProcessWebhook($data)
    {
        // Додайте вашу логіку для визначення, чи потрібно обробляти цей webhook
        // Наприклад, перевірка, чи є зміни на конкретній дошці або в конкретному списку
        return true;
    }

    private function isCardMoved($data)
    {
        // Перевірка, чи це подія переміщення карточки
        return isset($data['action']['type'])
            && $data['action']['type'] === 'updateCard'
            && isset($data['action']['data']['listAfter'])
            && isset($data['action']['data']['listBefore']);
    }

    private function sendTelegramMessage($message)
    {
        // Ваш код відправлення повідомлення в Telegram
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_GROUP_CHAT_ID');

        $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        if ($response->successful()) {
            return $response->json();
        }
    }

    private function saveUserFromStartCommand($data)
    {
        // Перевірка, чи є команда "/start"
        if (isset($data['message']['text']) && $data['message']['text'] === '/start') {
            $chatId = $data['message']['chat']['id'];
            $firstName = $data['message']['from']['first_name'];

            // Збереження користувача в базі даних (припускаючи, що у вас є модель TelegramUser)
            $user = TelegramUser::firstOrNew(['chat_id' => $chatId]);
            $user->name = $firstName;
            $user->save();

            // Відправлення повідомлення в Telegram про успішне збереження користувача
            $this->sendTelegramMessage("Користувач $firstName збережений в базі даних.");
        }
    }
}
