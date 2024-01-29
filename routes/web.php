<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramBotController;
use App\Http\Controllers\TrelloWebhookController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
//Route::get('/', [TrelloWebhookController::class, 'handleWebhook']);
Route::get('/test-webhook', [TelegramBotController::class, 'handle']);

Route::group(['middleware' => ['web']], function () {
    Route::post('/trello-webhook', [TelegramBotController::class, 'handleWebhook']);
    Route::get('/trello-webhook', [TelegramBotController::class, 'handleWebhook']);
    Route::post('/webhook', [TrelloWebhookController::class, 'handleWebhook']);
    Route::get('/webhook', [TrelloWebhookController::class, 'handleWebhook']);
    Route::get('/test-send-telegram-message', [TrelloWebhookController::class, 'testSendTelegramMessage']);

});
