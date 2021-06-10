<?php

if (!function_exists('modalFramework')) {
    function modalFramework(): string
    {
        return config('livewire-ui-modal.framework');
    }
}
