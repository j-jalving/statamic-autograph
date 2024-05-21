<?php

namespace JJalving\Autograph;

use Statamic\Providers\AddonServiceProvider;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Permission;
use Statamic\Facades\User;

class ServiceProvider extends AddonServiceProvider
{
    /**
     * List of Autograph routes
     *
     * @var array
     */
    protected $routes = [
        'cp' => __DIR__ . '/../routes/cp.php',
    ];

    /**
     * List of modifiers supplied by Autograph
     *
     * @var array
     */
    protected $modifiers = [
        Modifiers\FullUrl::class,
    ];

    /**
     * Boot Autograph
     *
     * @return void
     */
    public function bootAddon(): void
    {
        $this->bootAddonPermissions()
            ->bootPublishables()
            ->bootAddonNav();
    }

    /**
     * Set up Autograph permissions
     *
     * @return static
     */
    protected function bootAddonPermissions(): static
    {
        Permission::group('autograph', 'Autograph', function () {
            Permission::register('generate signatures')->label(__('statamic-autograph::messages.generate_signatures'));
        });

        return $this;
    }

    /**
     * Set up files that should be published during install
     *
     * @return static
     */
    public function bootPublishables(): static
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/statamic/autograph.php' => config_path('/statamic/autograph.php'),
            ], 'config');
            $this->publishes([
                __DIR__ . '/../resources/views/autograph' => resource_path('/views/autograph'),
            ], 'templates');
        }

        return $this;
    }

    /**
     * Set up navigation items
     *
     * @return static
     */
    protected function bootAddonNav(): static
    {
        Nav::extend(function ($nav) {
            if ($this->userHasAutographPermissions()) {
                $nav->tools('Autograph')
                    ->route('autograph.index')
                    ->icon('<svg viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><g fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width=".7"><path d="m2.4 15.6c3.4.3 9.3-.7 9.9-4.6 0-.8-.5-1.8-1.3-2-2.3-.5-3.3 2.1-1.9 3.6.9 1.1 2.7 2.3 3.9 3"/><path d="m4.8 12.5-2.5 1.7.3-3 3.2-5.5 3.2-5.4 1.1.7 1.1.6-3.2 5.4z"/><path d="m2.6 11.2 2.2 1.3"/><path d="m8.2 1.6 2.4 1.4c.7.4.9 1.3.5 2l-1.4 2.4"/></g></svg>')
                    ->active('autograph');
            }
        });

        return $this;
    }

    /**
     * Check if the current user has the proper permissions
     *
     * @return boolean
     */
    private function userHasAutographPermissions(): bool
    {
        $user = User::current();

        return $user->can('generate signatures');
    }
}
