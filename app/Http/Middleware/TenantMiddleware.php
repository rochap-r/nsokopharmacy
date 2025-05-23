<?php

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Log;

class TenantMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // 1. Récupérer le tenant de plusieurs manières possibles (par ordre de priorité)
        $tenant = $this->resolveTenant($request);

        // 2. Si pas de tenant trouvé, rediriger vers la page d'identification
        if (!$tenant) {
            Log::warning('Tentative d\'accès sans tenant valide', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'ip' => $request->ip()
            ]);

            return redirect()->route('identification')
                   ->with('error', 'Pharmacie non trouvée ou inactive.');
        }

        // 3. Sauvegarder le tenant pour qu'il soit accessible partout
        $this->setTenantContext($request, $tenant);

        // 4. Vérifier que l'utilisateur a accès à ce tenant s'il est connecté
        if (Auth::check() && Auth::user()->tenant_id !== $tenant->id) {
            Log::warning('Tentative d\'accès à un tenant non autorisé', [
                'user_id' => Auth::id(),
                'user_tenant' => Auth::user()->tenant_id,
                'requested_tenant' => $tenant->id,
                'url' => $request->fullUrl()
            ]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('tenant.login', ['tenant' => $tenant->domain])
                   ->with('error', 'Vous n\'avez pas accès à cette pharmacie.');
        }

        $response = $next($request);

        // Pour les réponses qui ne sont pas des redirections, rendre le tenant disponible dans la session
        if (!$response instanceof \Illuminate\Http\RedirectResponse) {
            session(['tenant_id' => $tenant->id]);
        }

        return $response;
    }

    /**
     * Résout le tenant de plusieurs sources possibles
     */
    protected function resolveTenant(Request $request)
    {
        // 1. D'abord essayer depuis le paramètre de route
        $tenantParam = $request->route('tenant');
        if ($tenantParam) {
            $tenant = Tenant::where(function($query) use ($tenantParam) {
                $query->where('id', $tenantParam)
                      ->orWhere('domain', $tenantParam);
            })->where('active', true)->first();

            if ($tenant) {
                return $tenant;
            }
        }

        // 2. Essayer depuis les données de formulaire (pour les requêtes POST d'authentification)
        if ($request->has('tenant_id') && is_numeric($request->tenant_id)) {
            $tenant = Tenant::where('id', $request->tenant_id)
                           ->where('active', true)
                           ->first();
            if ($tenant) {
                return $tenant;
            }
        }

        // 3. Essayer depuis la session
        if (session()->has('tenant_id')) {
            $tenant = Tenant::where('id', session('tenant_id'))
                           ->where('active', true)
                           ->first();
            if ($tenant) {
                return $tenant;
            }
        }

        // 4. Essayer via l'entête HTTP X-Tenant-ID pour les API
        if ($request->headers->has('X-Tenant-ID')) {
            $tenant = Tenant::where('id', $request->header('X-Tenant-ID'))
                           ->where('active', true)
                           ->first();
            if ($tenant) {
                return $tenant;
            }
        }

        // Aucun tenant trouvé
        return null;
    }

    /**
     * Configure le contexte du tenant pour qu'il soit accessible partout
     */
    protected function setTenantContext(Request $request, Tenant $tenant)
    {
        // 1. Stockage dans le conteneur d'application (pour cette requête)
        app()->instance('tenant', $tenant);

        // 2. Partage avec toutes les vues
        view()->share('currentTenant', $tenant);

        // 3. Stockage en session (pour les prochaines requêtes)
        session(['tenant_id' => $tenant->id]);

        // 4. Ajouter au contexte de la requête pour les formulaires
        $request->merge(['tenant_id' => $tenant->id]);

        // 5. Si c'est une requête API, ajouter aux données de réponse
        if ($request->expectsJson()) {
            // Pour les API, on peut ajouter le tenant à toutes les réponses
            app()->singleton('tenant.api_context', function () use ($tenant) {
                return $tenant;
            });
        }
    }
}
