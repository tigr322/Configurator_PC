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
                <form method="POST" action="{{ route('user.update', $user->id) }}">
                    @csrf
                    @method('PUT')
            
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>
                            <input type="text" name="name" value="{{ $user->name }}" class="w-full" />
                        </td>
                        <td>
                            <input type="email" name="email" value="{{ $user->email }}" class="w-full" />
                        </td>
                        <td>
                            <input type="password" name="password" class="w-full" />
                        </td>
                        <td>
                            <input type="password" name="password_confirmation" class="w-full" />
                        </td>
                        <td>
                            <input type="number" name="admin" value="{{ $user->admin }}" class="w-full" />
                        </td>
                        <td>
                            <button type="submit">Сохранить1</button>
                        </td>
                    </tr>
                </form>
                                <form method="POST" action="{{ route('user.destroy', $user->id) }}">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button type="submit" class="text-sm">Удалить</x-danger-button>
                                </form>
                            </td>
                        </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
