<?php

namespace LivewireUI\Modal\Tests;

use Livewire\Livewire;
use LivewireUI\Modal\Modal;
use LivewireUI\Modal\Tests\Components\DemoModal;
use LivewireUI\Modal\Tests\Components\InvalidModal;

class LivewireModalTest extends TestCase
{
    public function testOpenModalEventListener(): void
    {
        // Demo modal component
        Livewire::component('demo-modal', DemoModal::class);

        // Event attributes
        $component = 'demo-modal';
        $componentAttributes = ['message' => 'Foobar'];
        $modalAttributes = ['hello' => 'world', 'closeOnEscape' => true, 'maxWidth' => '2xl', 'closeOnClickAway' => true, 'closeOnEscapeIsForceful' => true, 'dispatchCloseEvent' => false];

        Livewire::test(Modal::class)
            // Verify no active component is set
            ->assertSet('activeComponent', null)
            // Emit open modal event
            ->emit('openModal', $component, $componentAttributes, $modalAttributes)
            // Verify component is added to $components
            ->assertSet('components', function ($value) use ($component, $componentAttributes, $modalAttributes) {
                return is_array($value) && count($value) == 1 && array_shift($value) == [
                    'name'            => $component,
                    'attributes'      => $componentAttributes,
                    'modalAttributes' => $modalAttributes,
                ];
            })
            // Verify component is set to active
            ->assertNotSet('activeComponent', null)
            // Verify event is emitted to client
            ->assertEmitted('activeModalComponentChanged');
    }

    public function testOpenModalUniqueComponentId() : void
    {
        // Demo modal component
        Livewire::component('demo-modal', DemoModal::class);

        // Event attributes
        $component = 'demo-modal';
        $componentAttributes = ['message' => 'Foobar'];
        $modalAttributes = ['hello' => 'world', 'closeOnEscape' => true, 'maxWidth' => '2xl', 'closeOnClickAway' => true, 'closeOnEscapeIsForceful' => true, 'dispatchCloseEvent' => false];

        $firstComponentId = Livewire::test(Modal::class)
            ->emit('openModal', $component, $componentAttributes, $modalAttributes)
            ->get('activeComponent');

        $secondComponentId = Livewire::test(Modal::class)
            ->emit('openModal', $component, $componentAttributes, $modalAttributes)
            ->get('activeComponent');

        // Verify components with same attributes have different component ids
        $this->assertNotEquals($firstComponentId, $secondComponentId);
    }

    public function testModalReset(): void
    {
        Livewire::component('demo-modal', DemoModal::class);

        Livewire::test(Modal::class)
            ->emit('openModal', 'demo-modal')
            ->set('components', [
                'some-component' => [
                    'name'            => 'demo-modal',
                    'attributes'      => 'bar',
                    'modalAttributes' => [],
                ],
            ])
            ->set('activeComponent', 'some-component')
            ->call('resetState')
            // Verify properties are reset
            ->assertSet('activeComponent', null)
            ->assertSet('components', []);
    }

    public function testIfExceptionIsThrownIfModalDoesNotImplementContract(): void
    {
        $component = InvalidModal::class;
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("[{$component}] does not implement [LivewireUI\Modal\Contracts\ModalComponent] interface.");

        Livewire::component('invalid-modal', $component);
        Livewire::test(Modal::class)->emit('openModal', 'invalid-modal');
    }
}
