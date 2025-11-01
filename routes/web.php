<?php

use App\Http\Controllers\Admin\ParserController;
use App\Http\Controllers\AiChatController;
use App\Http\Controllers\Components\ComponentController;
use App\Http\Controllers\Configuration\ConfigurationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

// Конфигуратор
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/configurator', [ConfigurationController::class, 'create'])->name('configurator');
    Route::post('/configurations', [ConfigurationController::class, 'store'])->name('configurations.store');
});
Route::get('/builds', [ConfigurationController::class, 'configurations'])->name('builds');

// Каталог/компоненты
Route::get('/catalog', [ComponentController::class, 'index'])->name('catalog');
Route::get('/component/{id}', [ComponentController::class, 'show'])->name('components.show');
Route::get('/components/{component}/chart', [ComponentController::class, 'chart'])->name('components.chart');
Route::post('/configurator/check-compatibility', [ComponentController::class, 'checkCompatibility']);
Route::post('/configurator/check-compatibility-multi', [ComponentController::class, 'checkCompatibilityMulti'])->name('checkCompatibilityMulti');

// CRUD по конфигурациям
Route::get('/configurations', [ConfigurationController::class, 'configurations'])->name('configurations');
Route::put('builds/{build}', [ConfigurationController::class, 'update'])->name('builds.update');
Route::delete('builds/{build}', [ConfigurationController::class, 'destroy'])->name('builds.destroy');
Route::get('/public-build/{id}', [ConfigurationController::class, 'publicShow'])->name('builds.public');

// Маркеты/URL
Route::get('/admin/get-urls', [ComponentController::class, 'getUrlsByMarket'])->name('admin.get.urls');
Route::get('/markets-urls/{market}', [ComponentController::class, 'getUrls'])->name('markets_urls.get');

// Комментарии
Route::post('/comments', [ConfigurationController::class, 'comments'])->name('comments.store');
Route::delete('/comments/{comment}', [ConfigurationController::class, 'destroyComments'])->name('comments.destroy');

// Голосование
Route::post('/configurations/{configuration}/like', [ConfigurationController::class, 'like'])->name('configurations.like');
Route::post('/configurations/{configuration}/dislike', [ConfigurationController::class, 'dislike'])->name('configurations.dislike');
Route::post('/configurations/{configuration}/best', [ConfigurationController::class, 'bestBuild'])->name('configurations.bestBuild');

// Переключение режима конфигуратора
Route::post('/toggle-configurator-mode', [ConfigurationController::class, 'toggleMode'])
    ->name('toggleConfiguratorMode')
    ->middleware('auth');

// Admin Parser (под auth)
Route::middleware(['auth'])->group(function () {
    Route::post('/admin/parse', [ParserController::class, 'parse'])->name('admin.parse');
    Route::post('/admin/addCategory', [ParserController::class, 'addCategory'])->name('admin.addCategory');
    Route::post('/admin/addMarket', [ParserController::class, 'addMarket'])->name('admin.addMarket');
    Route::post('/admin/addComponent', [ParserController::class, 'addComponent'])->name('admin.addComponent');
});

// Профиль/пользователи
Route::middleware('auth')->group(function () {
    // профиль
    Route::get('/profile', [ProfileController::class, 'editProfile'])->name('profile.editProfile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // админка пользователей (если используешь)
    Route::get('/profile/users', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/user/{user}', [ProfileController::class, 'updateUsers'])->name('user.update');
    Route::delete('/users/{user}', [ProfileController::class, 'destroyUser'])->name('user.destroy');

    // markets urls
    Route::post('/markets-urls/save', [ProfileController::class, 'saveMarketUrl'])->name('markets_urls.save');
    Route::delete('/markets-urls/delete/{id}', [ProfileController::class, 'destroyMarketUrl'])->name('markets_urls.destroy');
});

// AI чат (legacy)
Route::post('/ai-chat', [AiChatController::class, 'chat'])->name('ai.chat');

// AI Assistant (YandexGPT)
Route::middleware('auth')->group(function () {
    Route::get('/ai', [AiChatController::class, 'index'])->name('ai.index');
    Route::post('/ai/generate', [AiChatController::class, 'generate'])->name('ai.generate');
});


require __DIR__.'/auth.php';
