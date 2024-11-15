<?php

namespace Azuriom\Plugin\Trad\Providers;

use Azuriom\Extensions\Plugin\BasePluginServiceProvider;
use Azuriom\Models\Permission;

class TradServiceProvider extends BasePluginServiceProvider
{
    /**
     * The plugin's global HTTP middleware stack.
     *
     * @var array
     */
    protected array $middleware = [
        \Azuriom\Plugin\Trad\Middleware\Localization::class
    ];

    /**
     * Register any plugin services.
     *
     * @return void
     */
    public function register()
    {
        $this->middleware($this->middleware, true);
    }

    /**
     * Bootstrap any plugin services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViews();

        $this->loadTranslations();

        $this->registerRouteDescriptions();

        $this->registerUserNavigation();
        $this->registerAdminNavigation();

        Permission::registerPermissions([
            'trad.admin' => 'trad::permissions.admin',
            'trad.public' => 'trad::permissions.public'
        ]);
    }

    /**
     * Returns the routes that should be able to be added to the navbar.
     *
     * @return array
     */
    protected function routeDescriptions()
    {
        return [
            'trad.index' => trans('trad::messages.title'),
        ];
    }

    protected function userNavigation() {
        return [
            'trad' => [
                'route' => 'trad.index',
                'name' => trans('trad::public.translate'),
                'permission' => 'trad.public'
            ],
        ];
    }

    /**
     * Return the admin navigations routes to register in the dashboard.
     *
     * @return array
     */
    protected function adminNavigation()
    {
        return [
            'trad' => [
                'name'       => trans('trad::admin.title'), // Traduction du nom de l'onglet
                'permission' => 'trad.admin',
                'icon'       => 'bi bi-person-lines-fill', // Icône FontAwesome
                'route'      => 'trad.admin.index'
            ],
        ];
    }
}
