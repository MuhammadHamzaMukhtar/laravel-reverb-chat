<?php

use App\Events\MessageSent;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/dashboard', function () {
    return view('dashboard', [
        'users' => User::whereKeyNot(auth()->id())->get()
    ]);
})->middleware(['auth'])->name('dashboard');

Route::get('/chat/{friend}', function (User $friend) {
    return view('chat', compact('friend'));
})->middleware(['auth'])->name('chat');

Route::get('/messages/{friend}', function (User $friend) {
    return ChatMessage::where([
        'sender_id' => auth()->id(),
        'receiver_id' => $friend->id
    ])->orWhere([
        'sender_id' => $friend->id,
        'receiver_id' => auth()->id()
    ])->get();
})->middleware(['auth'])->name('messages');

Route::post('/messages/{friend}', function (User $friend) {
    $message = ChatMessage::create([
        'sender_id' => auth()->id(),
        'receiver_id' => $friend->id,
        'message' => request('message')
    ]);

    broadcast(new MessageSent($message));

    return $message;
})->middleware(['auth'])->name('messages');

require __DIR__ . '/auth.php';
