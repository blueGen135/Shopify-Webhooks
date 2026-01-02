<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Login extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    public function login()
    {
        $this->validate();

        // we must check the user's status before attempting login
        $user = \App\Models\User::where('email', $this->email)->first();
        if ($user && !$user->status) {
            $this->addError('email', 'Your account is disabled. Please contact the administrator.');
            return;
        }

        if (Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            session()->regenerate();

            return redirect()->intended('/dashboard');
        }

        $this->addError('email', 'The provided credentials do not match our records.');
    }

    #[Title('Login')]
    #[Layout('layouts.blankLayout')]
    public function render()
    {
        return view('livewire.auth.login');
    }
}
