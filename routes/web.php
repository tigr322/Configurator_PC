<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\Components\ComponentController;
use App\Http\Controllers\Configuration\ConfigurationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiChatController;
Route::get('/', function () {
    return view('welcome');
})->name('welcome');
//получаем категории компонетов в конфигуратор
Route::get('/configurator', [ConfigurationController::class,'create'])->middleware(['auth', 'verified'])->name('configurator');
//создание конфигурации и загрузки ее в бд
Route::post('/configurations', [ConfigurationController::class, 'store'])->middleware(['auth', 'verified'])->name('configurations.store');
//Route::get('/catalog', [ComponentController::class,'catalog'])->name('catalog');
//загрузка всех конфигурации из бд
Route::get('/builds', [ConfigurationController::class,'configurations'])->name(name: 'builds');
Route::get('/admin/get-urls', [ComponentController::class, 'getUrlsByMarket'])->name('admin.get.urls');

Route::middleware('auth')->group(function () {
    Route::get('/profile/users', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::get('/profile', [ProfileController::class, 'editProfile'])->name('profile.editProfile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    // Админска удаление и изменения статуса пользователя
   
    Route::put('/user/{user}', [ProfileController::class, 'updateUsers'])->name('user.update');
    
    Route::delete('/users/{user}', [ProfileController::class, 'destroyUser'])->name('user.destroy');

    Route::delete('/markets-urls/delete/{id}', [ProfileController::class, 'destroyMarketUrl'])->name('markets_urls.destroy');

    Route::post('/markets-urls/save/', [ProfileController::class, 'saveMarketUrl'])->name('markets_urls.save');

   
});

// дипсик помощник

Route::post('/ai-chat', action: [AiChatController::class, 'chat'])->name('ai.chat');

Route::get('/catalog', [ComponentController::class, 'index'])->name('catalog');
Route::get('/component/{id}', [ComponentController::class, 'show'])->name('components.show');

Route::get('/components/{component}/chart', [ComponentController::class, 'chart'])->name('components.chart');
//Route::get('/configurations/{id}', [ConfigurationController::class, 'show'])->name('configurationbuild.showconf');
Route::post('/configurator/check-compatibility', [ComponentController::class, 'checkCompatibility']);
Route::post('/configurator/check-compatibility-multi', [ComponentController::class, 'checkCompatibilityMulti'])->name('checkCompatibilityMulti');;
Route::post('/admin/parse-components', [App\Http\Controllers\Admin\ParserController::class, 'parse'])->name('admin.parse.components');
//configurations
Route::get('/configurations', [ConfigurationController::class, 'configurations'])->name('configurations');
Route::middleware(['auth'])->group(function () {
    Route::post('/admin/parse', [\App\Http\Controllers\Admin\ParserController::class, 'parse'])
        ->name('admin.parse');
        Route::post('/admin/addCategory', [\App\Http\Controllers\Admin\ParserController::class, 'addCategory'])
        ->name('admin.addCategory');
        Route::post('/admin/addMarket', [\App\Http\Controllers\Admin\ParserController::class, 'addMarket'])
        ->name('admin.addMarket');
        Route::post('/admin/addComponent', [\App\Http\Controllers\Admin\ParserController::class, 'addComponent'])
        ->name('admin.addComponent');
}); 
Route::delete('/delete/{id}', [ComponentController::class, 'delete'])->name('delete');
Route::put('/components/{id}', [ComponentController::class, 'update'])->name('components.update');

Route::post('/save-compatibility-rules', [ComponentController::class, 'saveRules'])->name('save.compatibility.rules');
Route::put('builds/{build}', [ConfigurationController::class, 'update'])->name('builds.update');
Route::delete('builds/{build}', [ConfigurationController::class, 'destroy'])->name('builds.destroy');
Route::get('/public-build/{id}', [ConfigurationController::class, 'publicShow'])->name('builds.public');
Route::get('/markets-urls/{market}', [ComponentController::class, 'getUrls'])->name('markets_urls.get');
//Комментарии
Route::post('/comments', [ConfigurationController::class, 'comments'])->name('comments.store');
// Управление комментариями
Route::delete('/comments/{comment}', [ConfigurationController::class, 'destroyComments'])->name('comments.destroy');
//Голосование за конфигурацию
Route::post('/configurations/{configuration}/like', [ConfigurationController::class, 'like'])->name('configurations.like');
Route::post('/configurations/{configuration}/dislike', [ConfigurationController::class, 'dislike'])->name('configurations.dislike');
Route::post('/configurations/{configuration}/best', [ConfigurationController::class, 'bestBuild'])->name('configurations.bestBuild');


Route::post('/toggle-configurator-mode', [ConfigurationController::class, 'toggleMode'])
    ->name('toggleConfiguratorMode')
    ->middleware('auth');
//Route::post('/compatibility/check', [ConfigurationController::class, 'checkCompatibility']);
//Конфигуратор
/* 
Route::middleware(['auth'])->group(function () {
   Route::get('/configurations/create', [ConfigurationController::class, 'create'])->name('configurations.create');
   
    //Route::get('/configurations', [ConfigurationController::class, 'index'])->name('configurations.index');
    //Route::get('/configurations/{id}', [ConfigurationController::class, 'show'])->name('configurations.show');
});
*/
require __DIR__.'/auth.php';
