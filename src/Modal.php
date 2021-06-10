<?php

namespace LivewireUI\Modal;

use Exception;
use Illuminate\View\View;
use Livewire\Component;
use ReflectionClass;

class Modal extends Component
{
    const TAILWIND  = 'tailwind';
    const BOOTSTRAP = 'bootstrap';

    public ?string $activeComponent;

    public array $components = [];

    public function resetState(): void
    {
        $this->components      = [];
        $this->activeComponent = null;
    }

    public function openModal($component, $componentAttributes = [], $modalAttributes = []): void
    {
        $requiredInterface = \LivewireUI\Modal\Contracts\ModalComponent::class;
        $componentClass    = app('livewire')->getClass($component);
        $reflect           = new ReflectionClass($componentClass);

        if ($reflect->implementsInterface($requiredInterface) === false) {
            throw new Exception("[{$componentClass}] does not implement [{$requiredInterface}] interface.");
        }

        $id                    = md5($component . serialize($componentAttributes));
        $this->components[$id] = [
            'name'            => $component,
            'attributes'      => $componentAttributes,
            'modalAttributes' => array_merge([
                'closeOnClickAway'          => $componentClass::closeModalOnClickAway(),
                'closeOnEscape'             => $componentClass::closeModalOnEscape(),
                'closeOnEscapeIsForceful'   => $componentClass::closeModalOnEscapeIsForceful(),
                'dispatchCloseEvent'        => $componentClass::dispatchCloseEvent(),
                'maxWidth'                  => $componentClass::modalMaxWidth(),
                'framework'                 => modalFramework(),
                'bsWidth'                   => $componentClass::bsModalWidth(),
                'bsTitle'                   => $componentClass::bsModalTitle()
            ], $modalAttributes),
        ];

        $this->activeComponent = $id;

        $this->emit('activeModalComponentChanged', $id);
    }

    public function getListeners(): array
    {
        return [
            'openModal',
        ];
    }

    public function render()
    {
        return view('livewire-ui::' . modalFramework());
    }
}
