<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use App\Http\Livewire\Traits\WithTenantContext;

#[Layout('components.layouts.auth.auth')]
class Login extends Component
{
    use WithTenantContext;
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;


    public function mount()
    {
        // Récupérer le paramètre tenant depuis la route
        $this->setTenant(app('tenant'));

        if (!$this->getCurrentTenant()) {
            return redirect()->route('identification');
        }

    }

    public function login(): void
    {
        $this->validate();

        $this->ensureIsNotRateLimited();


        // Récupérer le tenant à partir de l'ID stocké dans la propriété publique
        $tenant = \App\Models\Tenant::findOrFail($this->getCurrentTenant()->id);
        //dd($tenant);
        // Tentative d'authentification
        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Vérifier que l'utilisateur appartient bien au tenant
        $user = Auth::user();
        if ($user->tenant_id !== $tenant->id) {
            // Déconnecter l'utilisateur car il n'appartient pas à ce tenant
            Auth::logout();
            Session::invalidate();
            Session::regenerateToken();

            throw ValidationException::withMessages([
                'email' => 'Vous n\'avez pas accès à cette pharmacie. Veuillez contacter l\'administrateur.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        // Redirection vers le dashboard du tenant
        $this->redirectIntended(default: route('tenant.dashboard', ['tenant' => $tenant->domain], false), navigate: true);
    }

    /**
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }

    public function render()
    {
        $tenant = app('tenant');

        if (!$tenant) {
            return redirect()->route('identification');
        }

        return view('livewire.auth.login', [
            'title' => 'Connexion à ' . $tenant->name,
            'tenant' => $tenant
        ]);
    }

    /**
     * Toggle theme preferences
     */
    public function toggleTheme()
    {
        $theme = cookie('theme') === 'dark' ? 'light' : 'dark';
        cookie()->queue(cookie('theme', $theme, 60 * 24 * 365));
    }
}
