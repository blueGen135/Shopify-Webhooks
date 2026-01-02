<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ForgotPassword extends Component
{
    #[Validate('required|email')]
    public string $email = '';

    public bool $sent = false;

    public function sendResetLink()
    {
        $this->validate();

        $status = Password::sendResetLink(['email' => $this->email]);

        if ($status === Password::RESET_LINK_SENT) {
            $this->sent = true;
            $this->email = '';
        } else {
            $this->addError('email', 'Unable to send password reset link.');
        }
    }

    #[Title('Forgot Password')]
    #[Layout('layouts.blankLayout')]
    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
