<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    use HasFactory;
    protected $table = 'telegram_chats';
    protected $fillable = ['chat_id','name', 'telegram_bot_id'];

    public function routeNotificationForTelegram()
    {
        return 494761070;
    }
}
