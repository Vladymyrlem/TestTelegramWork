<?php

namespace App\Http\Telegraph;
use App\Http\Controllers\TrelloWebhookController;
use DefStudio\Telegraph\Exceptions\TelegraphException;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Models\TelegraphChat;
use DefStudio\Telegraph\DTO\User as TelegramUser;
use Illuminate\Support\Facades\Log;
class Qwerty extends WebhookHandler
{
    public function start()
{
$info = $this->chat->info();
$username = $info['first_name'] ?? 'Unknown';
    $this->chat->html("Привіт $username")->send();
//    Log::info($info);

}
    public function hello(string $name): void
    {
        $this->reply("Привет, $name!");
    }
    public function help(): void
    {
        $this->reply('*Привет!* Пока я имею только говорить привет.');
    }
    public function subscribe(): void
    {
        $this->reply("Спасибо за подписку на {$this->data->get('channel_name')}");
    }

}
