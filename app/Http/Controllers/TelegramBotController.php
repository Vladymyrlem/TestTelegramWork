<?php

namespace App\Http\Controllers;

use App\Http\Telegraph\Qwerty;
use DefStudio\Telegraph\Facades\Telegraph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    public function handleWebhook(Request $request)
    {
        $apiKey = env('TRELLO_API_KEY');
        $apiToken = env('TRELLO_API_TOKEN');
        $callbackUrl = route('trello-webhook');
        $boardId = env('TRELLO_BOARD_ID'); // Замініть на конкретний ідентифікатор вашої дошки
        $response = Http::post("https://api.trello.com/1/tokens/{$apiToken}/webhooks", [
            'key' => $apiKey,
            'callbackURL' => $callbackUrl,
            'token' => $apiToken,
            'idModel' => $boardId,
            // Додайте інші параметри, які вам потрібні для реєстрації webhook
        ]);
        // Get the raw content of the request
        $data = json_decode($response, true);
        dd($data);
        // Log received data
        \Illuminate\Support\Facades\Log::info('Received data:', ['data' => $data]);

        // Decode the received JSON data
        $decodedData = json_decode($data, true);

        // ... existing code ...

        // Process incoming Telegram update data here
//        if (isset($decodedData['message']['text'])) {
            $messageText = $decodedData['message']['text'];
            $chatId = $decodedData['message']['chat']['id'];
            $firstName = $decodedData['message']['from']['first_name'];

            // Log relevant information
            \Illuminate\Support\Facades\Log::info("Received message: $messageText from $firstName in chat $chatId");

            // Check if the user sent the "/start" command
//            if ($messageText === '/test') {
                // Compose the greeting message with the user's name
                $greetingMessage = "Hello, {$firstName}! Welcome to your Telegram bot.";

                // Log the greeting message
                \Illuminate\Support\Facades\Log::info("Sending greeting message: $greetingMessage");
//                $qwertyHandler = new Qwerty();
//                $qwertyHandler->start();
                // Send the greeting message back to the user
                $this->sendTelegramMessage($greetingMessage);
//            }
//        }

        // Log successful response to the Telegram API
        \Illuminate\Support\Facades\Log::info('Sending successful response to Telegram API');
        // Return a successful response to the Telegram API
        return response()->json(['success' => true, 'status' => 'ok']);
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
    public function handle(Request $request, Telegraph $telegraph)
    {
        $updates = $telegraph->getWebhookUpdates($request->getContent());

        if (isset($updates['message']['text'])) {
            $text = $updates['message']['text'];
            $chatId = $updates['message']['chat']['id'];

            // Check if the user sent the "/start" command
            if ($text === '/start') {
                $firstName = $updates['message']['from']['first_name'];
                $greetingMessage = "Hello, {$firstName}! Welcome to your Telegram bot.";

                // Send the greeting message back to the user
                $telegraph->sendMessage($chatId, $greetingMessage);
            }
        }

        return response()->json(['success' => true, 'status' => 'ok']);
    }
}
