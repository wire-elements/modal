{{-- w-screen max-w-sm max-w-md max-w-lg max-w-xl max-w-2xl max-w-3xl max-w-4xl max-w-5xl max-w-6xl max-w-7xl max-w-full duration-2000--}}
<div>
    @isset($jsPath)
        <script>{!! file_get_contents($jsPath) !!}</script>
    @endisset
    @isset($cssPath)
        <style>{!! file_get_contents($cssPath) !!}</style>
    @endisset

    @teleport('body')
        <div
            x-data="LivewireUIModal()"
            x-on:close.stop="setShowPropertyTo(false)"
            x-on:keydown.escape.window="closeModalOnEscape()"
            x-show="show"
            class="fixed inset-0 z-40 overflow-y-auto"
            style="display: none;"
        >
            <div class="w-screen h-full pointer-events-auto" 
                x-bind:class="modalWidth"
            >
                @foreach($components as $id => $component)
                    <div class="flex flex-col h-full" x-show="activeComponent == '{{ $id }}'" x-ref="{{ $id }}" wire:key="{{ $id }}">
                        @livewire($component['name'], $component['arguments'], key($id))
                    </div>
                @endforeach
            </div>
        </div>
    @endteleport
</div>