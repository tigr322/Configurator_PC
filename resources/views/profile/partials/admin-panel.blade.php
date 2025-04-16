<section>
    <header>
        <h2 class="text-lg font-medium">
            {{ __('Администрирование пользователей') }}
        </h2>
        <p class="mt-1 text-sm">
            {{ __('Редактируйте имя, email, пароль и права доступа пользователей.') }}
        </p>
    </header>

    <div class="mt-6 overflow-x-auto">
        <table class="border rounded-lg shadow-sm">
            <thead class="bg-gray-50">
                <tr>
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
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-xs text-green-600 mt-1"
                                    >
                                        {{ __('Сохранено.') }}
                                    </p>
                                @else
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-xs text-red-600 mt-1"
                                    >
                                        {{ __('Несохранено.') }}
                                    </p>
                                    
                            
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
</section>
