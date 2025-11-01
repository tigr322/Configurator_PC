<section>
    @php($users = isset($users) ? $users : collect())

    <header>
        <h2 class="text-lg font-medium">Панель администрирования пользователей</h2>
        <p class="mt-1 text-sm">Редактируйте имя, email, права администратора и пароль пользователей.</p>
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
                    <th class="px-4 py-2 text-left text-sm font-medium">Действия</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <tr>
                        <td class="px-4 py-2 align-top">{{ $user->id }}</td>
                        <td class="px-4 py-2 align-top">
                            <x-text-input form="update-user-{{ $user->id }}" name="name" value="{{ $user->name }}" class="w-full" required />
                        </td>
                        <td class="px-4 py-2 align-top">
                            <x-text-input form="update-user-{{ $user->id }}" name="email" value="{{ $user->email }}" type="email" class="w-full" required />
                        </td>
                        <td class="px-4 py-2 align-top">
                            <x-text-input form="update-user-{{ $user->id }}" name="password" type="password" placeholder="Новый пароль" class="w-full" />
                        </td>
                        <td class="px-4 py-2 align-top">
                            <x-text-input form="update-user-{{ $user->id }}" name="password_confirmation" type="password" placeholder="Повторите пароль" class="w-full" />
                        </td>
                        <td class="px-4 py-2 align-top">
                            <x-text-input form="update-user-{{ $user->id }}" name="admin" type="number" value="{{ $user->admin ?? 0 }}" class="w-full" />
                        </td>
                        <td class="px-4 py-2 align-top">
                            <button type="submit" form="update-user-{{ $user->id }}" class="px-3 py-1 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700">Сохранить</button>
                            <button type="submit" form="delete-user-{{ $user->id }}" class="ml-2 px-3 py-1 text-sm font-medium text-white bg-red-600 rounded hover:bg-red-700" onclick="return confirm('Удалить пользователя?')">Удалить</button>

                            @if (session('status') === 'user-updated-'.$user->id)
                                <p class="text-xs text-green-600 mt-2">Сохранено.</p>
                            @endif
                        </td>
                    </tr>

                    <form id="update-user-{{ $user->id }}" method="POST" action="{{ route('user.update', $user->id) }}" class="hidden">
                        @csrf
                        @method('PUT')
                    </form>

                    <form id="delete-user-{{ $user->id }}" method="POST" action="{{ route('user.destroy', $user->id) }}" class="hidden">
                        @csrf
                        @method('DELETE')
                    </form>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500">Пользователи не найдены.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</section>

