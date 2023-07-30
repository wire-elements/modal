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
        $arguments = ['user' => 1, 'number' => 42, 'message' => 'Hello World'];
        $modalAttributes = ['hello' => 'world', 'closeOnEscape' => true, 'maxWidth' => '2xl',  'maxWidthClass' => 'sm:max-w-md md:max-w-xl lg:max-w-2xl', 'closeOnClickAway' => true, 'closeOnEscapeIsForceful' => true, 'dispatchCloseEvent' => false, 'destroyOnClose' => false];

        // Demo modal unique identifier
        $id = md5($component.serialize($arguments));

        Livewire::test(Modal::class)
            ->dispatch('openModal', component: $component, arguments: $arguments, modalAttributes: $modalAttributes)
            // Verify component is added to $components
            ->assertSet('components', [
                $id => [
                    'name' => $component,
                    'arguments' => $arguments,
                    'attributes' => $arguments, // Deprecated
                    'modalAttributes' => $modalAttributes,
                ],
            ])
            // Verify component is set to active
            ->assertSet('activeComponent', $id)
            // Verify event is emitted to client
            ->assertDispatched('activeModalComponentChanged', id: $id)
            // Verif if component attribute 'message' is visible
            ->assertSee(['Hello World', 1, '42']);
    }

    public function testDestroyComponentEventListener(): void
    {
        // Demo modal component
        Livewire::component('demo-modal', DemoModal::class);

        $component = 'demo-modal';
        $arguments = ['message' => 'Foobar'];
        $modalAttributes = ['hello' => 'world', 'closeOnEscape' => true, 'maxWidth' => '2xl', 'maxWidthClass' => 'sm:max-w-md md:max-w-xl lg:max-w-2xl', 'closeOnClickAway' => true, 'closeOnEscapeIsForceful' => true, 'dispatchCloseEvent' => false, 'destroyOnClose' => false];

        // Demo modal unique identifier
        $id = md5($component.serialize($arguments));

        Livewire::test(Modal::class)
            ->dispatch('openModal', component: $component, arguments: $arguments, modalAttributes: $modalAttributes)
            ->assertSet('components', [
                $id => [
                    'name' => $component,
                    'arguments' => $arguments,
                    'attributes' => $arguments, // Deprecated
                    'modalAttributes' => $modalAttributes,
                ],
            ])
            ->dispatch('destroyComponent', $id)
            ->assertSet('components', []);
    }

    public function testModalReset(): void
    {
        Livewire::component('demo-modal', DemoModal::class);

        Livewire::test(Modal::class)
            ->dispatch('openModal', 'demo-modal')
            ->set('components', [
                'some-component' => [
                    'name' => 'demo-modal',
                    'arguments' => ['bar'],
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
        Livewire::test(Modal::class)->dispatch('openModal', component: 'invalid-modal');
    }
}
