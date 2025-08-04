@extends('layouts.navigation')

@section('title', 'Профиль')

@section('content')
<div class="flex justify-center">
    <div class="w-full max-w-4xl px-4 py-12">
        <div class="space-y-6">
            <div class="p-4 sm:p-8 shadow sm:rounded-lg">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 shadow sm:rounded-lg">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
            @if (auth()->check() && auth()->user()->admin == 1)
            <div class="container mx-auto px-4">
                <div class="text-center">
                    <h2 class="text-xl font-semibold">Все пользователи</h2>
                    @include('profile.partials.admin-panel')
                </div>
            </div>
            @endif
            <div class="p-4 sm:p-8 shadow sm:rounded-lg">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>

            <!-- Вывод всех пользователей -->
          
        </div>
    </div>
</div>
@endsection
