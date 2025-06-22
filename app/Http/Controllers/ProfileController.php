<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;
use App\Models\MarketsUrls;
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
        
       
        $usersQuery = User::query();
        
        if ($request->filled('name')) {
            $usersQuery->where('name', 'like', '%' . $request->name . '%');
        }
        if ($request->filled('email')) {
            $usersQuery->where('email', 'like', '%' . $request->email . '%');
        }
        
        $builds = Configurations::where('user_id', $authUser->id)
                              ->with('components')
                              ->get();
        
        $data = [
            'user' => $authUser,
            'builds' => $builds,
            'users' => $authUser->admin == 1 ? $usersQuery->get() : collect()
        ];
        
        return view('admin.admin-panel-users', $data);
    }
    public function editProfile(Request $request): View
    {   
        $authUser = $request->user();
        
       
        $builds = Configurations::where('user_id', $authUser->id)
                              ->with('components')
                              ->get();
        
        $data = [
            'user' => $authUser,
            'builds' => $builds,
            
        ];
        
        return view('profile.edit', $data);
    }
    public function destroyUser(User $user)
{
    $user->delete();
    return back()->with('status', 'user-deleted');
}
public function updateUsers(Request $request, User $user)
{
    $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        'admin' => ['nullable', 'integer', 'max:1'],
    ];

    if ($request->filled('password')) {
        $rules['password'] = [
            'confirmed',
            Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised()
        ];
    }
    $validated = $request->validate($rules);

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
    public function saveMarketUrl(Request $request)
{
    $marketId = $request->input('market_id');
    $urls = $request->input('urls');

    foreach ($urls as $data) {
        if (empty($data['url'])) {
            continue;
        }

        MarketsUrls::updateOrCreate(
            [
                'market_id' => $marketId,
                'category_id' => $data['category_id']
            ],
            [
                'url' => $data['url']
            ]
        );
    }

    return redirect()->back()->with('success', 'Ссылки успешно сохранены');
}
public function destroyMarketUrl($id)
{
    $url = MarketsUrls::find($id);

    if (!$url) {
        return response()->json([
            'success' => false,
            'message' => 'URL не найден'
        ], 404);
    }

    try {
        $url->delete();
        return response()->json([
            'success' => true,
            'message' => 'URL успешно удален'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Ошибка при удалении: ' . $e->getMessage()
        ], 500);
    }
}
    


}
