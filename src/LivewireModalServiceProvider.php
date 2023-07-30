<?php

namespace LivewireUI\Modal;

use Livewire\Features\SupportConsoleCommands\Commands\UpgradeCommand;
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

    public function registeringPackage()
    {
        UpgradeCommand::addThirdPartyUpgradeStep(WireElementsModalUpgrade::class);
    }

    public function bootingPackage(): void
    {
        Livewire::component('livewire-ui-modal', Modal::class);
        Livewire::component('wire-elements-modal', Modal::class);
    }
}
