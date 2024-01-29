<?php

namespace App\Http\Controllers;

use DefStudio\Telegraph\Facades\Telegraph;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramBotController extends Controller
{
    public function handleWebhook(Request $request)
    {
        // Get the raw content of the request
        $data = json_decode($request->getContent(), true);

        // Log received data
        \Illuminate\Support\Facades\Log::info('Received data:', ['data' => $data]);

        // Decode the received JSON data
        $decodedData = json_decode($data, true);

        // ... existing code ...

        // Process incoming Telegram update data here
        if (isset($decodedData['message']['text'])) {
            $messageText = $decodedData['message']['text'];
            $chatId = $decodedData['message']['chat']['id'];
            $firstName = $decodedData['message']['from']['first_name'];

            // Log relevant information
            \Illuminate\Support\Facades\Log::info("Received message: $messageText from $firstName in chat $chatId");

            // Check if the user sent the "/start" command
            if ($messageText === '/start') {
                // Compose the greeting message with the user's name
                $greetingMessage = "Hello, {$firstName}! Welcome to your Telegram bot.";

                // Log the greeting message
                \Illuminate\Support\Facades\Log::info("Sending greeting message: $greetingMessage");

                // Send the greeting message back to the user
                $this->sendTelegramMessage($chatId, $greetingMessage);
            }
        }

        // Log successful response to the Telegram API
        \Illuminate\Support\Facades\Log::info('Sending successful response to Telegram API');

        // Return a successful response to the Telegram API
        return response()->json(['success' => true, 'status' => 'ok']);
    }

    private function sendTelegramMessage($chatId, $message)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');

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
