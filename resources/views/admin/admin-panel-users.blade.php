<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Администрирование пользователей</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>

@include('layouts.navigation')

<body>
   

    <div class="relative h-screen">

        <div class="container mx-auto px-4 py-3">
        <div class="container mx-auto px-4 py-3">
        <h2 class="text-xl font-bold mb-6" style="font-size: 20px;" >Поиск</h2>
        <form method="GET" action="{{ route('profile.edit') }}" class="mb-3 flex flex-col gap-2">
           

            <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;"  type="text" name="name" placeholder="имя" value="{{ request('name') }}" class="border p-2 rounded">

            <input style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;"  type="text" name="email" placeholder="email" value="{{ request('почта') }}" class="border p-2 rounded">
            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded col-span-1 md:col-span-2">Применить</button>
        </form>
    </div>
        <table class="border rounded-lg shadow-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-medium">ID</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Имя</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Email</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Пароль</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Подтверждение</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Админ</th>
                    <th class="px-4 py-2 text-left text-sm font-medium">Действие</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $user)
                    <tr class="">
                        <form method="post" action="{{ route('user.update', $user->id) }}">
                            @csrf
                            @method('put')
                            <td class="px-4 py-2">
                               {{ $user->id }}
                            </td>
                            <td class="px-4 py-2">
                                <x-text-input  style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" name="name" value="{{ $user->name }}" class="w-full" required />
                            </td>
                            <td class="px-4 py-2">
                                <x-text-input name="email"  style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" value="{{ $user->email }}" type="email" class="w-full" required />
                            </td>
                            <td class="px-4 py-2">
                                <x-text-input name="password"  style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" type="password" placeholder="••••••••" class="w-full" />
                            </td>
                            <td class="px-4 py-2">
                                <x-text-input name="password_confirmation" style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" type="password" placeholder="••••••••" class="w-full" />
                            </td>
                            <td class="px-4 py-2">
                                <x-text-input name="admin" type="number"  style="background-color: #f3f4f6; padding: 0.5rem; border-radius: 0.25rem; font-size: 0.875rem; overflow-x: auto; color: black;" value="{{ $user->admin ?? '' }}" class="w-full" />
                            </td>
                            <td class="px-4 py-2">
                                <x-primary-button class="text-sm">{{ __('Сохранить') }}</x-primary-button>
                               
                                @if (session('status') === 'user-updated-'.$user->id)
                                <!-- Сообщение об успешном сохранении -->
                                <div 
                                    x-data="{ show: true }" 
                                    x-show="show"
                                    x-transition
                                    x-init="setTimeout(() => show = false, 2000)"
                                    class="text-xs text-green-600 mt-1 flex items-center"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    {{ __('Данные успешно сохранены!') }}
                                </div>
                            @elseif (session('status') === 'error')
                               
                                <div 
                                    x-data="{ show: true }" 
                                    x-show="show"
                                    x-transition
                                    x-init="setTimeout(() => show = false, 2000)"
                                    class="text-xs text-red-600 mt-1 flex items-center"
                                >
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    {{ __('Ошибка сохранения!') }}
                                </div>
                            @endif
                                    
                            
                        </form>
                        <form method="POST" action="{{ route('user.destroy', $user->id) }}" class="inline">
                            @csrf
                            @method('DELETE')
                            <x-danger-button type="submit" class="text-sm">
                                {{ __('Удалить') }}
                            </x-danger-button>
                        </form>
                    </td>
                    </tr>
                   
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</body>
</html>