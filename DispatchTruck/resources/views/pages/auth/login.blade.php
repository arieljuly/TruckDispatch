@extends('layouts.auth.split')

@section('content')
    <div class="flex flex-col gap-6">
        <div class="flex flex-col space-y-2 text-center">
            <h1 class="text-2xl font-semibold tracking-tight text-gray-900">{{ __('Hello, welcome back!') }}</h1>
            <p class="text-sm text-gray-600">{{ __('Sign in to get started!') }}</p>
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

        <form method="POST" action="{{ route('login') }}" class="space-y-4" id="loginForm">
            @csrf

            <!-- Email Address -->
            <div class="space-y-2">
                <label for="email" class="text-sm font-medium text-gray-700">
                    {{ __('Email address') }}
                </label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                    autocomplete="email" placeholder="johndoe@email.com"
                    class="flex h-10 w-full rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500" />
            </div>

            <!-- Password with Eye Toggle -->
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
                <div class="relative">
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                        placeholder="********"
                        class="w-full h-10 pr-10 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900" />
                    <button type="button"
                        class="toggle-password absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Remember Me -->
            <div class="flex items-center space-x-2">
                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" />
                <label for="remember" class="text-sm text-gray-700">
                    {{ __('Remember me') }}
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 font-medium"
                id="submitBtn">
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Toggle password visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function(e) {
                e.preventDefault();
                const input = this.parentElement.querySelector('input');
                if (!input) return;
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                const svg = this.querySelector('svg');
                if (svg) {
                    if (type === 'text') {
                        svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>';
                    } else {
                        svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>';
                    }
                }
            });
        });

        // Form submission loading state
        const form = document.getElementById('loginForm');
        const submitBtn = document.getElementById('submitBtn');

        if (form) {
            form.addEventListener('submit', function(e) {
                // Don't prevent default - let the form submit normally
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = 'Logging in...';
                }
            });
        }
    });

    // Handle back button navigation for authenticated users
    window.addEventListener('pageshow', function(event) {
        // Check if this is a back/forward navigation
        if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
            window.location.reload();
        }
    });
</script>
@endsection