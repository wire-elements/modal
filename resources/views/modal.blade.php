<div>
    @isset($jsPath)
        <script>{!! file_get_contents($jsPath) !!}</script>
    @endisset
    @isset($cssPath)
        <style>{!! file_get_contents($cssPath) !!}</style>
    @endisset

    <div
            x-data="LivewireUIModal()"
            x-init="init()"
            x-on:close.stop="show = false"
            x-on:keydown.escape.window="closeModalOnEscape()"
            x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
            x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
            x-show="show"
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;"
    >
    <div
        x-show="show"
        x-on:click="closeModalOnClickAway()"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 transition-all transform"
    >
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>
    <div class="fixed inset-0">
        <div class="fixed inset-y-0 right-0 flex" x-bind:class="modalWidth" x-bind:class="{ 'pl-10' : fullWidthOnMobile}" aria-labelledby="slide-over-heading">
            <div 
                class="flex"
                x-show="show"
                x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" 
                x-transition:enter-start="translate-x-full" 
                x-transition:enter-end="translate-x-0"
                x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" 
                x-transition:leave-start="translate-x-0" 
                x-transition:leave-end="translate-x-full"
            >
                {{-- <div x-html="modalWidth"></div> --}}
                @forelse($components as $id => $component)
                    @if($component['modalAttributes']['type'] == 'slide-over')
                        <div class="flex" x-show.immediate="activeComponent == '{{ $id }}'" x-ref="{{ $id }}" wire:key="{{ $id }}">
                            @livewire($component['name'], $component['attributes'], key($id))
                        </div>
                    @endif
                @empty
                @endforelse
            </div>
        </div>
        </div>

        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-10 text-center sm:block sm:p-0">


            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                    x-show="show && showActiveComponent"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-bind:class="modalWidth"
                    class="inline-block w-full align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:w-full"
            >
                @forelse($components as $id => $component)
                    @if($component['modalAttributes']['type'] == 'modal')
                        <div x-show.immediate="activeComponent == '{{ $id }}'" x-ref="{{ $id }}" wire:key="{{ $id }}">
                            @livewire($component['name'], $component['attributes'], key($id))
                        </div>
                    @endif
                @empty
                @endforelse
            </div>

            
        </div>
    </div>
</div>