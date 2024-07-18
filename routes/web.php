<?php

use App\Http\Controllers\BoardController;
use App\Http\Controllers\ThreadController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('home'));
Route::get('/{board}', [BoardController::class, 'show'])->name('board.show');
Route::get('/{board}/{thread}', [ThreadController::class, 'show'])->name('thread.show');
Route::post('/{board}/post', [ThreadController::class, 'store'])->name('thread.store');
Route::post('/{board}/{thread}/reply', [ThreadController::class, 'reply'])->name('thread.reply');
