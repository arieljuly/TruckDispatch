@extends('layouts.auth.split')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col space-y-2 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ __('Log in to your account') }}</h1>
        <p class="text-sm text-gray-600">{{ __('Enter your email and password below to log in') }}</p>
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

    <form method="POST" action="{{ route('login.store') }}" class="space-y-4">
        @csrf

        <!-- Email Address -->
        <div class="space-y-2">
            <label for="email" class="text-sm font-medium text-gray-700">
                {{ __('Email address') }}
            </label>
            <input
                type="email"
                name="email"
                id="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="email"
                placeholder="name@example.com"
                class="flex h-9 w-full rounded-md border border-gray-300 bg-white px-3 py-1 text-sm text-gray-900 shadow-sm transition-colors focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <div class="flex items-center justify-between">
                <label for="password" class="text-sm font-medium text-gray-700">
                    {{ __('Password') }}
                </label>
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500">
                        {{ __('Forgot password?') }}
                    </a>
                @endif
            </div>
            <input
                type="password"
                name="password"
                id="password"
                required
                autocomplete="current-password"
                placeholder="Enter your password"
                class="flex h-9 w-full rounded-md border border-gray-300 bg-white px-3 py-1 text-sm text-gray-900 shadow-sm transition-colors focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center space-x-2">
            <input
                type="checkbox"
                name="remember"
                id="remember"
                {{ old('remember') ? 'checked' : '' }}
                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
            />
            <label for="remember" class="text-sm text-gray-700">
                {{ __('Remember me') }}
            </label>
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
        >
            {{ __('Log in') }}
        </button>
    </form>

    <!-- Register Link -->
    @if (Route::has('register'))
        <div class="text-center text-sm text-gray-600">
            {{ __("Don't have an account?") }}
            <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-500 font-medium">
                {{ __('Sign up') }}
            </a>
        </div>
    @endif
</div>
@endsection
