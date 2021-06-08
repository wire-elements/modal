<?php

namespace LivewireUI\Modal;

use Livewire\Component;
use LivewireUI\Modal\Contracts\ModalComponent as Contract;

abstract class ModalComponent extends Component implements Contract
{
    public bool $forceClose = false;

    public int $skipModals = 0;

    public function skipPreviousModals($count = 1): self
    {
        $this->skipPreviousModal($count);

        return $this;
    }

    public function skipPreviousModal($count = 1): self
    {
        $this->skipModals = $count;

        return $this;
    }

    public function forceClose(): self
    {
        $this->forceClose = true;

        return $this;
    }

    public function closeModal(): void
    {
        $this->emit('closeModal', $this->forceClose, $this->skipModals);
    }

    public function closeModalWithEvents(array $events): void
    {
        $this->closeModal();
        $this->emitModalEvents($events);
    }

    public static function modalMaxWidth(): string
    {
        return '2xl';
    }

    public static function closeModalOnClickAway(): bool
    {
        return true;
    }

    public static function closeModalOnEscape(): bool
    {
        return true;
    }

    public static function closeModalOnEscapeIsForceful(): bool
    {
        return true;
    }

    public static function dispatchCloseEvent(): bool
    {
        return false;
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
