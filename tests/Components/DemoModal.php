<?php

namespace LivewireUI\Modal\Tests\Components;

use LivewireUI\Modal\ModalComponent;

class DemoModal extends ModalComponent
{
    public function render()
    {
        return <<<'blade'
            <div>
                Hello
            </div>
        blade;
    }
}
