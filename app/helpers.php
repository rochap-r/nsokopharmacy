<?php

if (!function_exists('current_tenant')) {
    /**
     * Get the current tenant instance.
     *
     * @return \App\Models\Tenant|null
     */
    function current_tenant()
    {
        return app('tenant');
    }
}

if (!function_exists('tenant_route')) {
    /**
     * Generate a route URL with the current tenant.
     *
     * @param string $name
     * @param array $parameters
     * @param bool $absolute
     * @return string
     */
    function tenant_route(string $name, array $parameters = [], bool $absolute = true)
    {
        $tenant = current_tenant();

        if ($tenant) {
            if (!isset($parameters['tenant'])) {
                $parameters['tenant'] = $tenant->domain;
            }
        }

        return route($name, $parameters, $absolute);
    }
}
