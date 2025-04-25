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
    <div class="overflow-x-auto">
        <table class="min-w-full border rounded-lg shadow-sm">
            <thead class="hidden sm:table-header-group">
                <tr>
                    <th class="px-2 sm:px-4 py-2 text-left text-xs sm:text-sm font-medium">ID</th>
                    <th class="px-2 sm:px-4 py-2 text-left text-xs sm:text-sm font-medium">Имя</th>
                    <th class="px-2 sm:px-4 py-2 text-left text-xs sm:text-sm font-medium">Email</th>
                    <th class="px-2 sm:px-4 py-2 text-left text-xs sm:text-sm font-medium">Пароль</th>
                    <th class="px-2 sm:px-4 py-2 text-left text-xs sm:text-sm font-medium">Подтверждение</th>
                    <th class="px-2 sm:px-4 py-2 text-left text-xs sm:text-sm font-medium">Админ</th>
                    <th class="px-2 sm:px-4 py-2 text-left text-xs sm:text-sm font-medium">Действие</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($users as $user)
                    <tr class="block sm:table-row mb-4 sm:mb-0 border-b sm:border-b-0">
                        <!-- Для мобильных - заголовок перед значением -->
                        <td class="px-2 sm:px-4 py-2 block sm:table-cell">
                            <span class="sm:hidden font-medium">ID: </span>
                            {{ $user->id }}
                        </td>
                        
                        <td class="px-2 sm:px-4 py-2 block sm:table-cell">
                            <span class="sm:hidden font-medium">Имя: </span>
                            <input name="name" value="{{ $user->name }}" 
                                         class="w-full sm:w-40 bg-gray-50 p-2 rounded text-sm text-black" required />
                        </td>
                        
                        <td class="px-2 sm:px-4 py-2 block sm:table-cell">
                            <span class="sm:hidden font-medium">Email: </span>
                            <input name="email" value="{{ $user->email }}" type="email"
                                         class="w-full sm:w-40 bg-gray-50 p-2 rounded text-sm text-black" required />
                        </td>
                        
                        <td class="px-2 sm:px-4 py-2 block sm:table-cell">
                            <span class="sm:hidden font-medium">Пароль: </span>
                            <input name="password" type="password" placeholder="••••••••"
                                         class="w-full sm:w-40 bg-gray-50 p-2 rounded text-sm text-black" />
                        </td>
                        
                        <td class="px-2 sm:px-4 py-2 block sm:table-cell">
                            <span class="sm:hidden font-medium">Подтверждение: </span>
                            <input name="password_confirmation" type="password" placeholder="••••••••"
                                         class="w-full sm:w-40 bg-gray-50 p-2 rounded text-sm text-black" />
                        </td>
                        
                        <td class="px-2 sm:px-4 py-2 block sm:table-cell">
                            <span class="sm:hidden font-medium">Админ: </span>
                            <input name="admin" type="number" value="{{ $user->admin ?? '' }}"
                                         class="w-full sm:w-40 bg-gray-50 p-2 rounded text-sm text-black" />
                        </td>
                        
                        <td class="px-2 sm:px-4 py-2 block sm:table-cell space-y-2 sm:space-y-0 sm:space-x-2">
                            <form method="post" action="{{ route('user.update', $user->id) }}" class="inline-block">
                                @csrf
                                @method('put')
                                <x-primary-button class="text-xs sm:text-sm w-full sm:w-auto">{{ __('Сохранить') }}</x-primary-button>
                                
                                @if (session('success') === 'user-updated-'.$user->id)
                                    <div x-data="{ show: true }" x-show="show" x-transition
                                         x-init="setTimeout(() => show = false, 2000)"
                                         class="text-xs text-green-600 mt-1 flex items-center">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ __('Сохранено!') }}
                                    </div>
                                @elseif (session('error')=== 'user-updated-'.$user->id)
                                    <div x-data="{ show: true }" x-show="show" x-transition
                                         x-init="setTimeout(() => show = false, 2000)"
                                         class="text-xs text-red-600 mt-1 flex items-center">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                        {{ __('Ошибка!') }}
                                    </div>
                                @endif
                            </form>
                            
                            <form method="POST" action="{{ route('user.destroy', $user->id) }}" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <x-danger-button type="submit" class="text-xs sm:text-sm w-full sm:w-auto mt-2 sm:mt-0">
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
</div>
</body>
</html>