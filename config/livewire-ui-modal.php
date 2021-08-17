<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Include CSS
    |--------------------------------------------------------------------------
    |
    | The modal uses TailwindCSS, if you don't use TailwindCSS you will need
    | to set this parameter to true. This includes the modern-normalize css.
    |
    */
    'include_css' => false,


    /*
    |--------------------------------------------------------------------------
    | Include JS
    |--------------------------------------------------------------------------
    |
    | Livewire UI will inject the required Javascript in your blade template.
    | If you want to bundle the required Javascript you can set this to false
    | and add `require('vendor/livewire-ui/modal/resources/js/modal');`
    | to your script bundler like webpack.
    |
    */
    'include_js' => true,


    /*
    |--------------------------------------------------------------------------
    | Focusables Selector
    |--------------------------------------------------------------------------
    |
    | Query Selector string to return focusable elements in a modal.
    |
    */
    'focusables_selector' => 'a, button, input, textarea, select, details, td, [tabindex]:not([tabindex=\'-1\'])'
];
