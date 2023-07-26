<?php

namespace LivewireUI\Modal;

use Livewire\Features\SupportConsoleCommands\Commands\Upgrade\UpgradeStep;
use Livewire\Features\SupportConsoleCommands\Commands\UpgradeCommand;

class WireElementsModalUpgrade extends UpgradeStep
{
    public function handle(UpgradeCommand $console, \Closure $next)
    {
        $this->interactiveReplacement(
            console: $console,
            title: 'The $dispatch helper expects named parameters.',
            before: '$dispatch(\'openModal\', \'component-name\', {user: 1})',
            after: '$dispatch(\'openModal\', {component: \'component-name\', parameters: {user: 1}})',
            pattern: '/\$(?:dispatch|emit)\(\'openModal\', \'([^\']+)\'(?:, (\{[^}]+\}|@js\(\[[^\]]+\]\)))?\)/',
            replacement: function($matches) {
                $component = $matches[1];
                $params = isset($matches[2]) ? ', parameters: ' . $matches[2] : '';
                return "\$dispatch('openModal', {component: '$component'$params)";
            },
            directories: 'resources'
        );

        $this->interactiveReplacement(
            console: $console,
            title: 'The Livewire.dispatch helper expects named parameters.',
            before: 'Livewire.dispatch(\'openModal\', \'component-name\', {user: 1})',
            after: 'Livewire.dispatch(\'openModal\', {component: \'component-name\', parameters: {user: 1}})',
            pattern: '/Livewire.(?:dispatch|emit)\(\'openModal\', \'([^\']+)\'(?:, (\{[^}]+\}|@js\(\[[^\]]+\]\)))?\)/',
            replacement: function($matches) {
                $component = $matches[1];
                $params = isset($matches[2]) ? ', parameters: ' . $matches[2] : '';
                return "Livewire.dispatch('openModal', {component: '$component'$params)";
            },
            directories: 'resources'
        );

        return $next($console);
    }
}