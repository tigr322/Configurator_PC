<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Configurations;
use App\Models\MarketsUrls;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Страница редактирования профиля текущего пользователя.
     */
    public function editProfile(Request $request): View
    {
        $authUser = $request->user();

        $builds = Configurations::where('user_id', $authUser->id)
            ->with('components')
            ->get();

        // Provide users list for admin panel partial to avoid undefined variable
        $users = (int) $authUser->admin === 1
            ? User::query()->get()
            : collect();

        return view('profile.edit', [
            'user'   => $authUser,
            'builds' => $builds,
            'users'  => $users,
        ]);
    }

    /**
     * Админ: список пользователей (если нужен отдельный экран админки пользователей).
     */
    public function edit(Request $request): View
    {
        $authUser = $request->user();

        $usersQuery = User::query();
        if ($request->filled('name')) {
            $usersQuery->where('name', 'like', '%'.$request->string('name').'%');
        }
        if ($request->filled('email')) {
            $usersQuery->where('email', 'like', '%'.$request->string('email').'%');
        }

        return view('admin.admin-panel-users', [
            'user'   => $authUser,
            'builds' => Configurations::where('user_id', $authUser->id)->with('components')->get(),
            'users'  => (int)$authUser->admin === 1 ? $usersQuery->get() : collect(),
        ]);
    }

    /**
     * Обновление полей своего профиля (имя, email и т.д.).
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.editProfile')->with('status', 'profile-updated');
    }

    /**
     * Удаление своего аккаунта.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Админ: обновление другого пользователя.
     */
    public function updateUsers(Request $request, User $user): RedirectResponse
    {
        // При желании повесь middleware is_admin, тут кратко:
        abort_unless((int)$request->user()->admin === 1, 403);

        $rules = [
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'admin' => ['nullable', 'boolean'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = [
                'confirmed',
                Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised(),
            ];
        }

        $validated = $request->validate($rules);

        $user->name  = $validated['name'];
        $user->email = $validated['email'];

        if (array_key_exists('admin', $validated)) {
            $user->admin = (int) (bool) $validated['admin'];
        }

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('status', 'user-updated-'.$user->id);
    }

    /**
     * Админ: удаление пользователя.
     */
    public function destroyUser(Request $request, User $user): RedirectResponse
    {
        abort_unless((int)$request->user()->admin === 1, 403);

        if ($request->user()->id === $user->id) {
            return back()->with('status', 'cannot-delete-yourself');
        }

        $user->delete();

        return back()->with('status', 'user-deleted');
    }

    /**
     * Сохранение ссылок маркетов.
     */
    public function saveMarketUrl(Request $request): RedirectResponse
    {
        $request->validate([
            'market_id'          => ['required', 'integer', 'min:1'],
            'urls'               => ['array'],
            'urls.*.category_id' => ['required', 'integer', 'min:1'],
            'urls.*.url'         => ['nullable', 'string', 'max:2048'],
        ]);

        $marketId = (int) $request->input('market_id');
        $urls     = $request->input('urls', []);

        foreach ($urls as $data) {
            $url = trim((string) ($data['url'] ?? ''));
            if ($url === '') {
                continue;
            }

            MarketsUrls::updateOrCreate(
                [
                    'market_id'   => $marketId,
                    'category_id' => (int) $data['category_id'],
                ],
                [
                    'url' => $url,
                ]
            );
        }

        return back()->with('success', 'Ссылки успешно сохранены');
    }

    /**
     * Удаление ссылки маркета по id (JSON-ответ).
     */
    public function destroyMarketUrl(Request $request, int $id)
    {
        $url = MarketsUrls::find($id);

        if (!$url) {
            return response()->json(['success' => false, 'message' => 'URL не найден'], 404);
        }

        try {
            $url->delete();
            return response()->json(['success' => true, 'message' => 'URL успешно удалён']);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => 'Ошибка при удалении: '.$e->getMessage()], 500);
        }
    }
}
