<?php

namespace JJalving\Autograph;

use Statamic\Providers\AddonServiceProvider;
use Statamic\Facades\CP\Nav;
use Statamic\Facades\Permission;
use Statamic\Facades\User;

class ServiceProvider extends AddonServiceProvider
{

    protected $routes = [
        'cp' => __DIR__.'/../routes/cp.php',
    ];
    
    public function bootAddon()
    {
        $this->bootAddonPermissions()
             ->bootPublishables()
             ->bootAddonNav();
    }

    protected function bootAddonPermissions()
    {
        Permission::group('autograph', 'Autograph', function () {
            Permission::register('generate signatures')->label(__('statamic-autograph::messages.generate_signatures'));
        });

        return $this;
    }

    public function bootPublishables(): static
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/statamic/autograph.php' => config_path('/statamic/autograph.php'),
            ], 'config');
            $this->publishes([
                __DIR__.'/../resources/views/autograph' => resource_path('/views/autograph'),
            ], 'templates');
        }

        return $this;
    }

    protected function bootAddonNav()
    {
        Nav::extend(function ($nav) {
            if ($this->userHasAutographPermissions()) {
                $nav->tools('Autograph')
                    ->route('autograph.index')
                    ->icon('<svg enable-background="new 0 0 16 16" viewBox="0 0 16 16" xmlns="http://www.w3.org/2000/svg"><path d="m8.85-6.02c-.1 0-.19.05-.23.13l-6.16 10.54c-.02.04-.04.08-.04.12l-.2 2.81c-.01.15.1.27.25.28.06 0 .12-.01.17-.04l2.36-1.58c.03-.02.06-.05.08-.09l5.27-9.02.12.07c.49.28.65.88.37 1.36l-1.4 2.4c-.08.12-.04.28.09.36s.29.03.36-.09c0 0 0 0 0-.01l1.4-2.4c.42-.73.17-1.66-.56-2.08l-.12-.07.62-1.06c.07-.13.03-.29-.1-.36l-2.16-1.24c-.04-.02-.08-.03-.12-.03zm.09.62 1.7.97-.49.84-1.7-.97zm-.76 1.3 1.4.8.12.06.18.1-5.13 8.8-1.7-.97zm2.32 6.4c-.41 0-.81.13-1.15.33-.58.34-1.02.89-1.12 1.49-.11.62.09 1.22.43 1.76.29.47.69.9 1.13 1.3-.87.49-1.92.87-2.95 1.13-.97.25-1.91.4-2.66.46-.37.02-.7.03-.95.02-.25-.02-.44-.06-.48-.08-.13-.07-.29-.02-.36.11s-.01.29.12.36h.01c.18.09.39.12.68.14.28.02.63.01 1.02-.02.78-.06 1.75-.21 2.75-.47 1.13-.29 2.3-.7 3.26-1.27.99.81 2.06 1.4 2.49 1.62.13.07.29.02.36-.11s.02-.29-.11-.36c0 0 0 0-.01 0-.38-.19-1.35-.74-2.27-1.46.52-.37.96-.8 1.26-1.3.29-.49.51-1.13.48-1.78s-.36-1.3-1.1-1.67c-.25-.13-.51-.19-.77-.19-.02-.01-.04-.01-.06-.01zm-.01.53h.04c.19.01.38.05.56.14.58.29.78.72.81 1.23.03.5-.16 1.07-.4 1.48-.27.45-.71.86-1.24 1.22-.46-.41-.87-.85-1.15-1.3-.3-.48-.44-.94-.36-1.39.07-.4.41-.86.87-1.13.27-.15.57-.25.87-.25zm-7.57 2.39 1.42.81-1.55 1.04z" transform="translate(0 6.35)"/></svg>')
                    ->active('autograph');
            }
        });

        return $this;
    }

    private function userHasAutographPermissions()
    {
        $user = User::current();

        return $user->can('generate signatures');
    }
    
    protected $modifiers = [
        Modifiers\FullUrl::class,
    ];
}
