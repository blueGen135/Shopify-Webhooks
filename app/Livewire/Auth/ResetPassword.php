<?php

namespace App\Livewire\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ResetPassword extends Component
{
    public string $token = '';

    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string|min:8|confirmed')]
    public string $password = '';

    #[Validate('required|string|min:8')]
    public string $password_confirmation = '';

    public bool $tokenValid = false;

    public string $tokenError = '';

    public function mount(string $token)
    {
        $this->token = $token;
        $this->email = request()->email ?? '';

        // Validate token immediately in mount
        $this->validateToken();
    }

    private function validateToken(): void
    {
        // Check if email is provided in the URL
        if (empty($this->email)) {
            $this->tokenError = 'Invalid password reset link. Email parameter is missing.';
            $this->tokenValid = false;

            return;
        }

        // Check if token exists in database
        $resetRecord = DB::table('password_reset_tokens')
            ->where('email', $this->email)
            ->first();

        if (!$resetRecord) {
            $this->tokenError = 'This password reset link is invalid or has already been used.';
            $this->tokenValid = false;

            return;
        }

        // Check if token matches. Laravel's broker stores the token hashed,
        // so use Hash::check to verify the given token against the hashed value.
        if (!Hash::check($this->token, $resetRecord->token)) {
            $this->tokenError = 'This password reset link is invalid.';
            $this->tokenValid = false;

            return;
        }

        // Check if token has expired (default: 60 minutes)
        $passwordResetTimeout = config('auth.passwords.users.expire') * 60; // Convert minutes to seconds
        $tokenAge = now()->diffInSeconds($resetRecord->created_at);

        if ($tokenAge > $passwordResetTimeout) {
            $this->tokenError = 'This password reset link has expired. Please request a new one.';
            $this->tokenValid = false;
            return;
        }

        // Token is valid
        $this->tokenValid = true;
        $this->tokenError = '';
    }

    public function resetPassword()
    {
        // Check if token is still valid before attempting reset
        if (!$this->tokenValid) {
            $this->validateToken();
            if (!$this->tokenValid) {
                $this->addError('email', $this->tokenError);

                return;
            }
        }

        $this->validate();

        $status = Password::reset(
            [
                'email' => $this->email,
                'password' => $this->password,
                'password_confirmation' => $this->password_confirmation,
                'token' => $this->token,
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect('/')->with('status', 'Password has been reset!');
        }

        $this->addError('email', 'Password reset failed. Please try again.');
    }

    #[Title('Reset Password')]
    #[Layout('layouts.blankLayout')]
    public function render()
    {
        return view('livewire.auth.reset-password');
    }
}
