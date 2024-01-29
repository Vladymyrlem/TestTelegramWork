<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class TelegramBotController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Обробка вхідних даних від Trello
//        $data = $request->all();


        // Відправлення інформації до Telegram бота
//        $this->sendToTelegram($data);
        $data = json_decode($request->getContent(), true);

        // Process incoming Telegram update data here
        if (isset($data['message']['text'])) {
            $messageText = $data['message']['text'];
            $chatId = $data['message']['chat']['id'];
            $firstName = $data['message']['from']['first_name'];

            // Check if the user sent the "/start" command
            if ($messageText === '/start') {
                // Compose the greeting message with the user's name
                $greetingMessage = "Hello, {$firstName}! Welcome to your Telegram bot.";

                // Send the greeting message back to the user
                $this->sendToTelegram($chatId, $greetingMessage);
            }
        }

        // Повернення успішної відповіді на запит від Trello
        return response()->json(['success' => true,'status' => 'ok']);
    }

    private function sendToTelegram($chatId, $message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');

        $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $message,
        ]);

        if ($response->successful()) {
            return $response->json();
        } else {
            error_log("Telegram API error: " . $response->status());
            return ['success' => false, 'error' => $response->status()];
        }
    }
}
