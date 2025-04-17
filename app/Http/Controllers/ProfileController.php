<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Models\Configurations;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

use Illuminate\View\View;
use League\Config\Configuration;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
{
    $authUser = $request->user();
    
    $data = [
        'user' => $authUser,
        'builds' => Configurations::where('user_id', $authUser->id)
                                          ->with('components')
                                          ->get()
    ];
    
    // Добавляем список пользователей только для администратора
    if ($authUser->admin == 1) {
        $data['users'] = User::all();
    }
    
    return view('profile.edit', $data);
}
    public function destroyUser(User $user)
{
    $user->delete();
    return back()->with('status', 'user-deleted');
}
    public function updateUsers(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => [
        'nullable',
        'confirmed', // Добавляем проверку совпадения с password_confirmation
        Password::min(8) // Минимальная длина 8 символов
            ->mixedCase() // Требуются заглавные и строчные буквы
            ->numbers() // Требуются цифры
            ->symbols() // Требуются специальные символы
            ->uncompromised() // Проверка на утечку данных
    ],
          
            'admin' => ['nullable', 'integer', 'max:1'],
        ]);
    
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->admin = $validated['admin'] ?? $user->admin;
    
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
    
        $user->save();
    
        return back()->with('status', 'user-updated-' . $user->id);
    }
    
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
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

        return Redirect::to('/');
    }
}
