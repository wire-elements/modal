<?php

namespace LivewireUI\Modal\Tests\Components;

use LivewireUI\Modal\ModalComponent;
use LivewireUI\Modal\Tests\Models\TestUser;

class DemoModal extends ModalComponent
{
    public TestUser $user;

    public $number;

    public $message;

    public function mount(TestUser $user)
    {
        $this->user = $user;
    }

    public function render()
    {
        return <<<blade
            <div>
                {$this->user->first_name} says:
                {$this->message} + {$this->number}
            </div>
        blade;
    }
}
