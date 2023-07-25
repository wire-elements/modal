<?php

namespace LivewireUI\Modal\Tests\Components;

use LivewireUI\Modal\ModalComponent;

class DemoModal extends ModalComponent
{
    public $user;

    public $number;

    public $message;

    public function render()
    {
        return <<<blade
            <div>
                {$this->user} says:
                {$this->message} + {$this->number}
            </div>
        blade;
    }
}
