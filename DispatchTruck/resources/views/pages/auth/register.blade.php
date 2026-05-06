{{-- DispatchTruck/resources/views/pages/auth/register.blade.php --}}
@extends('layouts.auth.split')

@section('content')
<div class="w-full">
    <!-- Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">Create an account</h1>
        <p class="text-base text-gray-600">Join us today and get started with your journey</p>
    </div>

    <!-- Errors -->
    @if($errors->any())
        <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
            @foreach($errors->all() as $error)
                <p class="text-sm text-red-600">{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <!-- Form -->
    <form method="POST" action="{{ route('register') }}" class="space-y-5" id="registerForm">
        @csrf

        <!-- Name Fields - 3 columns -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- First Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="first_name" value="{{ old('first_name') }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                    placeholder="First name" required autofocus>
            </div>

            <!-- Middle Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Middle Name <span class="text-gray-400 text-xs font-normal">(Optional)</span>
                </label>
                <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                    placeholder="Middle name">
            </div>

            <!-- Last Name -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="last_name" value="{{ old('last_name') }}"
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                    placeholder="Last name" required>
            </div>
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Email Address <span class="text-red-500">*</span>
            </label>
            <input type="email" name="email" value="{{ old('email') }}"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                placeholder="your@email.com" required>
        </div>

        <!-- Phone Number -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1.5">
                Phone Number <span class="text-red-500">*</span>
            </label>
            <input type="tel" name="phone_number" value="{{ old('phone_number') }}"
                class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                placeholder="9123456789" required>
        </div>

        <!-- Password and Confirm - 2 columns -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" name="password" id="password"
                        class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                        placeholder="Create password" required>
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
                <div class="mt-1.5">
                    <div class="flex gap-1">
                        <div class="strength-bar flex-1 h-1 rounded bg-gray-200"></div>
                        <div class="strength-bar flex-1 h-1 rounded bg-gray-200"></div>
                        <div class="strength-bar flex-1 h-1 rounded bg-gray-200"></div>
                        <div class="strength-bar flex-1 h-1 rounded bg-gray-200"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1.5">
                        <span class="password-strength-text">Min. 8 characters with letters & numbers</span>
                    </p>
                </div>
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full px-4 py-2.5 pr-10 border border-gray-300 rounded-lg focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-gray-900"
                        placeholder="Confirm password" required>
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
                <div class="password-match mt-1.5 hidden">
                    <p class="text-xs text-green-600 flex items-center gap-1">
                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                            </path>
                        </svg>
                        Passwords match
                    </p>
                </div>
            </div>
        </div>

        <!-- Terms -->
        <div class="flex items-start gap-2 pt-2">
            <input type="checkbox" name="terms" id="terms" required
                class="mt-0.5 h-4 w-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
            <label for="terms" class="text-sm text-gray-600">
                I agree to the <a href="#" class="text-blue-600 hover:underline">Terms of Service</a> and
                <a href="#" class="text-blue-600 hover:underline">Privacy Policy</a>
            </label>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="w-full bg-blue-600 text-white py-2.5 px-4 rounded-lg hover:bg-blue-700 font-medium"
            id="submitBtn">
            Create Account
        </button>
    </form>

    <!-- Divider -->
    <div class="relative my-6">
        <div class="absolute inset-0 flex items-center">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center text-sm">
            <span class="px-4 bg-white text-gray-500">or continue with</span>
        </div>
    </div>

    <!-- Social Login -->
    <div class="grid grid-cols-2 gap-3 mb-6">
        <button class="flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700">
            <svg class="w-5 h-5" viewBox="0 0 24 24">
                <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
            </svg>
            <span class="text-sm">Google</span>
        </button>
        <button class="flex items-center justify-center gap-2 px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700">
            <svg class="w-5 h-5" fill="#1877f2" viewBox="0 0 24 24">
                <path d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"/>
            </svg>
            <span class="text-sm">Facebook</span>
        </button>
    </div>

    <!-- Login Link -->
    <p class="text-center text-sm text-gray-600">
        Already have an account?
        <a href="{{ route('login') }}" class="text-blue-600 hover:underline font-medium">Sign In</a>
    </p>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Check if there's a success message from session
    @if(session('success'))
        Swal.fire({
            title: '{{ session('success.title') }}',
            text: '{{ session('success.message') }}',
            icon: 'success',
            confirmButtonText: 'Go to Login',
            allowOutsideClick: false,
            allowEscapeKey: false,
            allowEnterKey: false
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route('login') }}';
            }
        });
    @endif

    // Check if there's an error message from session
    @if(session('error'))
        Swal.fire({
            title: 'Registration Failed',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'Try Again'
        });
    @endif

    // Password strength meter
    const passwordInput = document.getElementById('password');
    const confirmPasswordInput = document.getElementById('password_confirmation');
    const strengthBars = document.querySelectorAll('.strength-bar');
    const strengthText = document.querySelector('.password-strength-text');

    function checkPasswordStrength(password) {
        let strength = 0;
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]+/) && password.match(/[A-Z]+/)) strength++;
        if (password.match(/[0-9]+/)) strength++;
        if (password.match(/[$@#&!]+/)) strength++;
        return Math.min(strength, 4);
    }

    function updateStrengthMeter() {
        if (!passwordInput) return;
        const password = passwordInput.value;
        const strength = checkPasswordStrength(password);
        const colors = ['', 'bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'];
        const texts = ['', 'Weak', 'Fair', 'Good', 'Strong'];

        strengthBars.forEach((bar, index) => {
            bar.classList.remove('bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500');
            if (index < strength && strength > 0) {
                bar.classList.add(colors[strength]);
            } else {
                bar.classList.add('bg-gray-200');
            }
        });

        if (password.length > 0 && strengthText) {
            strengthText.textContent = texts[strength];
            strengthText.className = `text-xs mt-1.5 ${strength <= 1 ? 'text-red-500' : strength <= 2 ? 'text-orange-500' : strength <= 3 ? 'text-yellow-600' : 'text-green-600'}`;
        } else if (strengthText) {
            strengthText.textContent = 'Min. 8 characters with letters & numbers';
            strengthText.className = 'text-xs text-gray-500 mt-1.5';
        }
    }

    function checkPasswordMatch() {
        if (!passwordInput || !confirmPasswordInput) return;
        const matchDiv = document.querySelector('.password-match');
        if (confirmPasswordInput.value.length > 0) {
            if (passwordInput.value === confirmPasswordInput.value && matchDiv) {
                matchDiv.classList.remove('hidden');
            } else if (matchDiv) {
                matchDiv.classList.add('hidden');
            }
        } else if (matchDiv) {
            matchDiv.classList.add('hidden');
        }
    }

    if (passwordInput) {
        passwordInput.addEventListener('input', () => {
            updateStrengthMeter();
            checkPasswordMatch();
        });
    }

    if (confirmPasswordInput) {
        confirmPasswordInput.addEventListener('input', checkPasswordMatch);
    }

    // Toggle password visibility
    document.querySelectorAll('.toggle-password').forEach(button => {
        button.addEventListener('click', function () {
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
</script>
@endsection