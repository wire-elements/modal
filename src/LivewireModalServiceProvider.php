<?php

namespace LivewireUI\Modal;

use Illuminate\Support\Facades\View;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LivewireModalServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('wire-elements-modal')
            ->hasConfigFile()
            ->hasViews();
    }

    public function bootingPackage(): void
    {
        Livewire::component('wire-elements-modal', Modal::class);

        // View::composer('livewire-ui-modal::modal', function ($view) {
        //     if (config('livewire-ui-modal.include_js', true)) {
        //         $view->jsPath = __DIR__.'/../resources/js/modal.js';
        //     }

        //     if (config('livewire-ui-modal.include_css', false)) {
        //         $view->cssPath = __DIR__.'/../public/modal.css';
        //     }
        // });
    }
}
