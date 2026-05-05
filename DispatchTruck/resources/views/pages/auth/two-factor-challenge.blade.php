@extends('layouts.auth')

@section('content')
<div class="flex flex-col gap-6">
    <div
        class="relative w-full h-auto"
        x-data="{
            showRecoveryInput: {{ $errors->has('recovery_code') ? 'true' : 'false' }},
            code: '',
            recovery_code: '',
            focusOtp() {
                this.$nextTick(() => {
                    const inputs = document.querySelectorAll('.otp-input');
                    if (inputs.length) inputs[0]?.focus();
                });
            },
            init() {
                if (!this.showRecoveryInput) {
                    this.focusOtp();
                }
            },
            toggleInput() {
                this.showRecoveryInput = !this.showRecoveryInput;
                this.code = '';
                this.recovery_code = '';
                
                this.$nextTick(() => {
                    if (this.showRecoveryInput) {
                        document.getElementById('recovery_code')?.focus();
                    } else {
                        this.focusOtp();
                    }
                });
            }
        }"
    >
        <div x-show="!showRecoveryInput">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ __('Authentication code') }}</h1>
                <p class="text-gray-600 mt-2">{{ __('Enter the authentication code provided by your authenticator application.') }}</p>
            </div>
        </div>

        <div x-show="showRecoveryInput">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-900">{{ __('Recovery code') }}</h1>
                <p class="text-gray-600 mt-2">{{ __('Please confirm access to your account by entering one of your emergency recovery codes.') }}</p>
            </div>
        </div>

        @if($errors->any())
            <div class="text-sm text-red-600 bg-red-50 p-3 rounded text-center">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('two-factor.login.store') }}">
            @csrf

            <div class="space-y-5 text-center">
                <!-- OTP Input (6 digits) -->
                <div x-show="!showRecoveryInput">
                    <div class="flex justify-center gap-2 my-5">
                        <input 
                            type="text"
                            maxlength="1"
                            class="otp-input w-12 h-12 text-center text-2xl border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            x-model="code"
                            @input="
                                if ($event.target.value.length === 1) {
                                    let next = $event.target.nextElementSibling;
                                    if (next) next.focus();
                                }
                                code = Array.from(document.querySelectorAll('.otp-input')).map(i => i.value).join('');
                            "
                            @keydown.backspace="
                                if ($event.target.value === '') {
                                    let prev = $event.target.previousElementSibling;
                                    if (prev) prev.focus();
                                }
                            "
                        >
                        <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-2xl border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-2xl border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-2xl border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-2xl border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="text" maxlength="1" class="otp-input w-12 h-12 text-center text-2xl border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <input type="hidden" name="code" x-model="code">
                </div>

                <!-- Recovery Code Input -->
                <div x-show="showRecoveryInput">
                    <div class="my-5">
                        <input
                            type="text"
                            name="recovery_code"
                            id="recovery_code"
                            x-bind:required="showRecoveryInput"
                            autocomplete="one-time-code"
                            x-model="recovery_code"
                            placeholder="Recovery code"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        />
                    </div>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors"
                >
                    {{ __('Continue') }}
                </button>
            </div>

            <!-- Toggle between OTP and Recovery Code -->
            <div class="mt-5 text-center text-sm">
                <span class="text-gray-600">{{ __('or you can') }}</span>
                <button 
                    type="button"
                    @click="toggleInput()"
                    class="text-blue-600 hover:text-blue-500 ml-1 font-medium"
                >
                    <span x-show="!showRecoveryInput">{{ __('login using a recovery code') }}</span>
                    <span x-show="showRecoveryInput">{{ __('login using an authentication code') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
