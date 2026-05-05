@extends('layouts.auth')

@section('content')
<div class="flex flex-col gap-6">
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Reset password') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('Please enter your new password below') }}</p>
    </div>

    <!-- Session Status -->
    @if(session('status'))
        <div class="text-sm text-green-600 text-center bg-green-50 p-3 rounded">
            {{ session('status') }}
        </div>
    @endif

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="text-sm text-red-600 bg-red-50 p-3 rounded">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="flex flex-col gap-6">
        @csrf
        
        <!-- Token -->
        <input type="hidden" name="token" value="{{ request()->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Email address') }}
            </label>
            <input
                type="email"
                name="email"
                id="email"
                value="{{ request('email') }}"
                required
                autocomplete="email"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="you@example.com"
            />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('New password') }}
            </label>
            <input
                type="password"
                name="password"
                id="password"
                required
                autocomplete="new-password"
                placeholder="{{ __('Password') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Confirm password') }}
            </label>
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="{{ __('Confirm password') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
            />
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
            data-test="reset-password-button"
        >
            {{ __('Reset password') }}
        </button>
    </form>
</div>
@endsection
