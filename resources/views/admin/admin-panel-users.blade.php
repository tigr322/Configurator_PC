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
        <table class="min-w-full border rounded-lg shadow-sm">
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
            @forelse($users as $user)
                {{-- Форма обновления пользователя (одна форма на строку) --}}
                <form method="POST" action="{{ route('user.update', $user->id) }}">
                    @csrf
                    @method('PUT')
                    <tr>
                        <td class="px-4 py-2 align-middle">{{ $user->id }}</td>

                        <td class="px-4 py-2 align-middle">
                            <x-text-input
                                name="name"
                                value="{{ old('name', $user->name) }}"
                                class="w-full"
                            />
                        </td>

                        <td class="px-4 py-2 align-middle">
                            <x-text-input
                                name="email"
                                type="email"
                                value="{{ old('email', $user->email) }}"
                                class="w-full"
                            />
                        </td>

                        <td class="px-4 py-2 align-middle">
                            <x-text-input
                                name="password"
                                type="password"
                                placeholder="••••••••"
                                class="w-full"
                            />
                        </td>

                        <td class="px-4 py-2 align-middle">
                            <x-text-input
                                name="password_confirmation"
                                type="password"
                                placeholder="••••••••"
                                class="w-full"
                            />
                        </td>

                        <td class="px-4 py-2 align-middle">
                            <x-text-input
                                name="admin"
                                type="number"
                                value="{{ old('admin', (int) $user->admin) }}"
                                class="w-full"
                            />
                        </td>

                        <td class="px-4 py-2 align-middle">
                            <div class="flex items-center gap-2">
                                <x-primary-button class="text-sm">{{ __('Сохранить') }}</x-primary-button>

                                {{-- Отдельная форма удаления --}}
                                <form method="POST" action="{{ route('user.destroy', $user->id) }}" onsubmit="return confirm('Удалить пользователя #{{ $user->id }}?');">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit" class="text-sm">
                                        {{ __('Удалить') }}
                                    </x-danger-button>
                                </form>
                            </div>

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
                            @endif
                        </td>
                    </tr>
                </form>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">
                        {{ __('Пользователей нет') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</section>
