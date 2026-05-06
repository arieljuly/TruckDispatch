<?php

namespace App\Actions\Fortify;

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class),
            ],
            'phone_number' => ['required', 'string', 'max:20'],
            'password' => $this->passwordRules(),
        ])->validate();

        $clientRole = Role::where('role_name', 'client')->first();

        if (!$clientRole) {
            $clientRole = Role::create([
                'role_name' => 'client',
                'description' => 'Default client role'
            ]);
        }

        $user = User::create([
            'first_name' => $input['first_name'],
            'middle_name' => $input['middle_name'] ?? null,
            'last_name' => $input['last_name'],
            'email' => $input['email'],
            'phone_number' => $input['phone_number'],
            'password' => Hash::make($input['password']),
            'role_id' => $clientRole->id,
            'status' => 'active',
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
        ]);

        // Set a session flash message
        session()->flash('success', [
            'title' => 'Account Created Successfully!',
            'message' => 'Welcome to TruckDispatch! Your account has been created. Please login to continue.'
        ]);

        return $user;
    }
}