<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\Components\ComponentController;
use App\Http\Controllers\Configuration\ConfigurationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/configurator', [ConfigurationController::class,'create'])->middleware(['auth', 'verified'])->name('configurator');
Route::post('/configurations', [ConfigurationController::class, 'store'])->middleware(['auth', 'verified'])->name('configurations.store');
//Route::get('/catalog', [ComponentController::class,'catalog'])->name('catalog');
Route::get('/builds', [ConfigurationController::class,'configurations'])->name('builds');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::get('/catalog', [ComponentController::class, 'index'])->name('catalog');
Route::get('/component/{id}', [ComponentController::class, 'show'])->name('components.show');
Route::get('/configurations/{id}', [ConfigurationController::class, 'show'])->name('configurationbuild.showconf');


//Конфигуратор
/* 
Route::middleware(['auth'])->group(function () {
   Route::get('/configurations/create', [ConfigurationController::class, 'create'])->name('configurations.create');
   
    //Route::get('/configurations', [ConfigurationController::class, 'index'])->name('configurations.index');
    //Route::get('/configurations/{id}', [ConfigurationController::class, 'show'])->name('configurations.show');
});
*/
require __DIR__.'/auth.php';
