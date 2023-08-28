<?php

namespace LivewireUI\Modal\Tests;

use Livewire\Livewire;
use Livewire\Mechanisms\ComponentRegistry;
use LivewireUI\Modal\Tests\Components\DemoModal;

class LivewireModalComponentTest extends TestCase
{
    public function testCloseModal(): void
    {
        Livewire::test(DemoModal::class)
            ->call('closeModal')
            ->assertDispatched('closeModal', force: false, skipPreviousModals: 0, destroySkipped: false);
    }

    public function testForceCloseModal(): void
    {
        Livewire::test(DemoModal::class)
            ->call('forceClose')
            ->call('closeModal')
            ->assertDispatched('closeModal', force: true, skipPreviousModals: 0, destroySkipped: false);
    }

    public function testModalSkipping(): void
    {
        Livewire::test(DemoModal::class)
            ->call('skipPreviousModals', 5)
            ->call('closeModal')
            ->assertDispatched('closeModal', force: false, skipPreviousModals: 5, destroySkipped: false);

        Livewire::test(DemoModal::class)
            ->call('skipPreviousModal')
            ->call('closeModal')
            ->assertDispatched('closeModal', force: false, skipPreviousModals: 1, destroySkipped: false);

        Livewire::test(DemoModal::class)
            ->call('skipPreviousModal')
            ->call('destroySkippedModals')
            ->call('closeModal')
            ->assertDispatched('closeModal', force: false, skipPreviousModals: 1, destroySkipped: true);
    }

    public function testModalEventEmitting(): void
    {
        Livewire::test(DemoModal::class)
            ->call('closeModalWithEvents', [
                'someEvent',
            ])
            ->assertDispatched('someEvent');

        $name = app(ComponentRegistry::class)->getName(DemoModal::class);

        Livewire::test(DemoModal::class)
            ->call('closeModalWithEvents', [
                $name => 'someEvent',
            ])
            ->assertDispatched('someEvent');

        Livewire::test(DemoModal::class)
            ->call('closeModalWithEvents', [
                ['someEventWithParams', ['param1', 'param2']],
            ])
            ->assertDispatched('someEventWithParams', 'param1', 'param2');

        Livewire::test(DemoModal::class)
            ->call('closeModalWithEvents', [
                $name => ['someEventWithParams', ['param1', 'param2']],
            ])
            ->assertDispatched('someEventWithParams', 'param1', 'param2');
    }
}
