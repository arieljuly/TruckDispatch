@extends('layouts.auth.split')

@section('content')
    <div class="w-full">
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('Forgot password') }}</h1>
            <p class="text-base text-gray-600">{{ __('Enter your email to receive a password reset link') }}</p>
        </div>

        <!-- Session Status -->
        @if(session('status'))
            <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg text-center">
                <p class="text-sm text-green-600">{{ session('status') }}</p>
            </div>
        @endif

        <!-- Validation Errors -->
        @if($errors->any())
            <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                @foreach($errors->all() as $error)
                    <p class="text-sm text-red-600">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5" id="forgotPasswordForm">
            @csrf

            <!-- Email Address -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                    {{ __('Email address') }} <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                    placeholder="johndoe@email.com">
            </div>

            <!-- Submit Button -->
            <button type="submit" class="w-full bg-blue-600 text-white py-2.5 px-4 rounded-lg hover:bg-blue-700 font-medium"
                id="submitBtn">
                {{ __('Email password reset link') }}
            </button>
        </form>

        <!-- Back to Login Link -->
        <div class="text-center text-sm text-gray-600 mt-6">
            <span>{{ __('Back to ') }}</span>
            <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">
                {{ __('Sign in') }}
            </a>
        </div>
    </div>

    <script>
        // Form submission loading state
        const form = document.getElementById('forgotPasswordForm');
        const submitBtn = document.getElementById('submitBtn');

        if (form) {
            form.addEventListener('submit', function () {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '{{ __("Sending reset link...") }}';
                }
            });
        }
    </script>
@endsection