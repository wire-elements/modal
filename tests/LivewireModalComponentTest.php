<?php

namespace LivewireUI\Modal\Tests;

use Livewire\Livewire;
use LivewireUI\Modal\Tests\Components\DemoModal;

class LivewireModalComponentTest extends TestCase
{
    public function testCloseModal(): void
    {
        Livewire::test(DemoModal::class)
            ->call('closeModal')
            ->assertEmitted('closeModal', false, 0);
    }

    public function testForceCloseModal(): void
    {
        Livewire::test(DemoModal::class)
            ->call('forceClose')
            ->call('closeModal')
            ->assertEmitted('closeModal', true, 0);
    }

    public function testModalSkipping(): void
    {
        Livewire::test(DemoModal::class)
            ->call('skipPreviousModals', 5)
            ->call('closeModal')
            ->assertEmitted('closeModal', false, 5);

        Livewire::test(DemoModal::class)
            ->call('skipPreviousModal')
            ->call('closeModal')
            ->assertEmitted('closeModal', false, 1);
    }

    public function testModalEventEmitting(): void
    {
        Livewire::test(DemoModal::class)
            ->call('closeModalWithEvents', [
                'someEvent',
            ])
            ->assertEmitted('someEvent');

        Livewire::test(DemoModal::class)
            ->call('closeModalWithEvents', [
                DemoModal::getName() => 'someEvent',
            ])
            ->assertEmitted('someEvent');

        Livewire::test(DemoModal::class)
            ->call('closeModalWithEvents', [
                ['someEventWithParams', ['param1', 'param2']],
            ])
            ->assertEmitted('someEventWithParams', 'param1', 'param2');

        Livewire::test(DemoModal::class)
            ->call('closeModalWithEvents', [
                DemoModal::getName() => ['someEventWithParams', ['param1', 'param2']],
            ])
            ->assertEmitted('someEventWithParams', 'param1', 'param2');
    }
}
