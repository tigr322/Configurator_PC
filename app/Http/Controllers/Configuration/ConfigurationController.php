<?php

namespace App\Http\Controllers\Configuration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Configurations;
class ConfigurationController extends Controller
{
    public function configurations(Request $request){
        $builds = Configurations::all();  // Получаем все конфигурации

        return view('configurationbuild.builds', ['builds' => $builds]);  // Передаем данные в представление
    }
    public function show($id)
    {
        $configuration = Configurations::with('components.category')->findOrFail($id);
    
        return view('configurationbuild.showconf', compact('configuration')); // Убедитесь, что переменная правильно названа
    }
}
