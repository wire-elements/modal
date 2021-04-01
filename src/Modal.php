<?php

namespace LivewireUI\Modal;

use Exception;
use Livewire\Component;
use ReflectionClass;

class Modal extends Component
{
    public ?string $activeComponent;

    public array $components = [];

    public function resetState()
    {
        $this->components = [];
        $this->activeComponent = null;
    }

    public function openModal($component, $componentAttributes = [], $modalAttributes = [])
    {
        $requiredInterface = \LivewireUI\Modal\Contracts\ModalComponent::class;
        $componentClass = app('livewire')->getClass($component);
        $reflect = new ReflectionClass($componentClass);

        if ($reflect->implementsInterface($requiredInterface) === false) {
            throw new Exception("[{$componentClass}] does not implement [{$requiredInterface}] interface.");
        }

        $id = md5($component . serialize($componentAttributes));
        $this->components[$id] = [
            'name'            => $component,
            'attributes'      => $componentAttributes,
            'modalAttributes' => $modalAttributes,
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
        return view('livewire-ui::modal');
    }
}