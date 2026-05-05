@extends('layouts.auth.split')

@section('content')
<div class="flex flex-col gap-6">
    <div class="flex flex-col space-y-2 text-center">
        <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ __('Create an account') }}</h1>
        <p class="text-sm text-gray-600">{{ __('Enter your details below to create your account') }}</p>
    </div>

    <!-- Validation Errors -->
    @if($errors->any())
        <div class="text-sm text-red-600 bg-red-50 p-3 rounded">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('register.store') }}" class="space-y-4">
        @csrf

        <!-- Name -->
        <div class="space-y-2">
            <label for="name" class="text-sm font-medium text-gray-700">
                {{ __('Full name') }}
            </label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                placeholder="John Doe"
                class="flex h-9 w-full rounded-md border border-gray-300 bg-white px-3 py-1 text-sm text-gray-900 shadow-sm transition-colors focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            />
        </div>

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
                autocomplete="email"
                placeholder="name@example.com"
                class="flex h-9 w-full rounded-md border border-gray-300 bg-white px-3 py-1 text-sm text-gray-900 shadow-sm transition-colors focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            />
        </div>

        <!-- Password -->
        <div class="space-y-2">
            <label for="password" class="text-sm font-medium text-gray-700">
                {{ __('Password') }}
            </label>
            <input
                type="password"
                name="password"
                id="password"
                required
                autocomplete="new-password"
                placeholder="Create a password"
                class="flex h-9 w-full rounded-md border border-gray-300 bg-white px-3 py-1 text-sm text-gray-900 shadow-sm transition-colors focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            />
        </div>

        <!-- Confirm Password -->
        <div class="space-y-2">
            <label for="password_confirmation" class="text-sm font-medium text-gray-700">
                {{ __('Confirm password') }}
            </label>
            <input
                type="password"
                name="password_confirmation"
                id="password_confirmation"
                required
                autocomplete="new-password"
                placeholder="Confirm your password"
                class="flex h-9 w-full rounded-md border border-gray-300 bg-white px-3 py-1 text-sm text-gray-900 shadow-sm transition-colors focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500"
            />
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
        >
            {{ __('Create account') }}
        </button>
    </form>

    <!-- Login Link -->
    <div class="text-center text-sm text-gray-600">
        {{ __('Already have an account?') }}
        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-500 font-medium">
            {{ __('Log in') }}
        </a>
    </div>
</div>
@endsection
