<?php

namespace LivewireUI\Modal;

use Livewire\Component;
use LivewireUI\Modal\Contracts\ModalComponent as Contract;

abstract class ModalComponent extends Component implements Contract
{
    public bool $forceClose = false;

    public int $skipModals = 0;

    public bool $destroySkipped = false;

    public function destroySkippedModals(): self
    {
        $this->destroySkipped = true;
        
        return $this;
    }

    public function skipPreviousModals($count = 1, $destroy = false): self
    {
        $this->skipPreviousModal($count, $destroy);

        return $this;
    }

    public function skipPreviousModal($count = 1, $destroy = false): self
    {
        $this->skipModals = $count;
        $this->destroySkipped = $destroy;

        return $this;
    }

    public function forceClose(): self
    {
        $this->forceClose = true;

        return $this;
    }

    public function closeModal(): void
    {
        $this->emit('closeModal', $this->forceClose, $this->skipModals, $this->destroySkipped);
    }

    public function closeModalWithEvents(array $events): void
    {
        $this->closeModal();
        $this->emitModalEvents($events);
    }

    public static function modalMaxWidth(): string
    {
        return config('livewire-ui-modal.component_defaults.modal_max_width', '2xl');
    }

    public static function closeModalOnClickAway(): bool
    {
        return config('livewire-ui-modal.component_defaults.close_modal_on_click_away', true);
    }

    public static function closeModalOnEscape(): bool
    {
        return config('livewire-ui-modal.component_defaults.close_modal_on_escape', true);
    }

    public static function closeModalOnEscapeIsForceful(): bool
    {
        return config('livewire-ui-modal.component_defaults.close_modal_on_escape_is_forceful', true);
    }

    public static function dispatchCloseEvent(): bool
    {
        return config('livewire-ui-modal.component_defaults.dispatch_close_event', false);
    }

    public static function destroyOnClose(): bool
    {
        return config('livewire-ui-modal.component_defaults.destroy_on_close', false);
    }

    private function emitModalEvents(array $events): void
    {
        foreach ($events as $component => $event) {
            if (is_array($event)) {
                [$event, $params] = $event;
            }

            if (is_numeric($component)) {
                $this->emit($event, ...$params ?? []);
            } else {
                $this->emitTo($component, $event, ...$params ?? []);
            }
        }
    }
}
