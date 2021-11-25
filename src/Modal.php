<?php

namespace LivewireUI\Modal;

use Exception;
use Illuminate\View\View;
use Livewire\Component;
use ReflectionClass;

class Modal extends Component
{
    public ?string $activeComponent;

    public array $components = [];

    public function resetState(): void
    {
        $this->components = [];
        $this->activeComponent = null;
    }

    public function openModal($component, $componentAttributes = [], $modalAttributes = []): void
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
            'modalAttributes' => array_merge([
                'closeOnClickAway' => $componentClass::closeModalOnClickAway(),
                'closeOnEscape' => $componentClass::closeModalOnEscape(),
                'closeOnEscapeIsForceful' => $componentClass::closeModalOnEscapeIsForceful(),
                'dispatchCloseEvent' => $componentClass::dispatchCloseEvent(),
                'destroyOnClose' => $componentClass::destroyOnClose(),
                'maxWidth' => $componentClass::modalMaxWidth(),
                'maxWidthClass' => $componentClass::modalMaxWidthClass(),
            ], $modalAttributes),
        ];

        $this->activeComponent = $id;

        $this->emit('activeModalComponentChanged', $id);
    }

    public function destroyComponent($id): void
    {
        unset($this->components[$id]);
    }

   public function updateModalAttribute($attribute, bool $value): void
    {
        $updateableAttributes = [
            'closeOnClickAway',
            'closeOnEscape',
            'closeOnEscapeIsForceful',
            'dispatchCloseEvent',
            'destroyOnClose',
        ];

        if (isset($this->components[$this->activeComponent])) {
            if(in_array($attribute, $updateableAttributes)) {
                $this->components[$this->activeComponent]['modalAttributes'][$attribute] = $value;
            }
        }
    }

    public function activeModalComponentUpdated($id): void
    {
        $this->activeComponent = $id;
    }
    
    public function getListeners(): array
    {
        return [
            'openModal',
            'destroyComponent',
            'updateModalAttribute',
            'activeModalComponentUpdated',
        ];
    }

    public function render(): View
    {
        return view('livewire-ui-modal::modal');
    }
}
