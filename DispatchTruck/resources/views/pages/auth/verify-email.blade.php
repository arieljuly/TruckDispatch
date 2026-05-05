@extends('layouts.auth')

@section('content')
<div class="flex flex-col gap-6">
    <div class="text-center">
        <h1 class="text-2xl font-bold text-gray-900">{{ __('Email verification') }}</h1>
        <p class="text-gray-600 mt-2">{{ __('Verify your email address to continue') }}</p>
    </div>

    <div class="text-center text-gray-700">
        {{ __('Please verify your email address by clicking on the link we just emailed to you.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="text-center text-green-600 bg-green-50 p-3 rounded">
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="flex flex-col items-center space-y-3">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf
            <button 
                type="submit" 
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
            >
                {{ __('Resend verification email') }}
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button 
                type="submit" 
                class="text-sm text-gray-600 hover:text-gray-700"
                data-test="logout-button"
            >
                {{ __('Log out') }}
            </button>
        </form>
    </div>
</div>
@endsection
