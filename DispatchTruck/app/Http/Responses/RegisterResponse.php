<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\RegisterResponse as RegisterResponseContract;

class RegisterResponse implements RegisterResponseContract
{
    public function toResponse($request)
    {
        // Log out the user after registration
        auth()->logout();

        // Invalidate the session
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect back to register page with success message
        return redirect()->route('register')->with('success', [
            'title' => 'Account Created Successfully!',
            'message' => 'Welcome to TruckDispatch! Your account has been created. Please login to continue.'
        ]);
    }
}