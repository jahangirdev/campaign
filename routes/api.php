<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\TemplateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::post('contacts/subscribe/',  [ContactController::class, 'subscribe'])->name('contact.subscribe');
Route::post('templates/after-quiz/',  [TemplateController::class, 'get_after_quiz_template'])->name('template.get.afterquiz');
