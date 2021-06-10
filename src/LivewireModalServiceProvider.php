<?php

namespace LivewireUI\Modal;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;

class LivewireModalServiceProvider extends ServiceProvider
{
    public static array $scripts = ['modal.js'];

    public function boot(): void
    {
        $this->registerViews();

        $this->registerPublishables();

        $this->registerComponent();

        $this->registerConfig();
    }

    private function registerViews(): void
    {
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'livewire-ui');
    }

    private function registerComponent(): void
    {
        Livewire::component('livewire-ui-modal', Modal::class);
    }

    private function registerPublishables(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/views' => resource_path('views/vendor/livewire-ui'),
            ], 'livewire-ui:views');

            $this->publishes([
                __DIR__ . '/../resources/js' => resource_path('js/vendor/livewire-ui'),
            ], 'livewire-ui:scripts');

            $this->publishes([
                __DIR__ . '/../public' => public_path('vendor/livewire-ui'),
            ], 'livewire-ui:public');
        }
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../resources/config/livewire-ui-modal.php', 'livewire-ui-modal'
        );

        $file = __DIR__ . '/functions.php';
        if (file_exists($file)) {
            require_once($file);
        }
    }

    private function registerConfig()
    {
        $this->publishes([
            __DIR__ . '/../resources/config/livewire-ui-modal.php' => config_path('livewire-ui-modal.php'),
        ], 'livewire-ui:config');
    }
}
