<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class ForgotPassword extends Component
{
    public string $email = '';

    /**
     * Send a password reset link to the provided email address.
     */
    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        Password::sendResetLink($this->only('email'));

        session()->flash('status', __('A reset link will be sent if the account exists.'));
    }

    /**
     * Toggle theme preferences
     */
    public function toggleTheme()
    {
        $theme = cookie('theme') === 'dark' ? 'light' : 'dark';
        cookie()->queue(cookie('theme', $theme, 60 * 24 * 365));
    }

    public function render()
    {
        return view('livewire.auth.forgot-password', [
            'title' => 'Récupération de mot de passe'
        ]);
    }
}
