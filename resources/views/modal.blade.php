{{-- w-screen max-w-sm max-w-md max-w-lg max-w-xl max-w-2xl max-w-3xl max-w-4xl max-w-5xl max-w-6xl max-w-7xl max-w-full duration-2000--}}
<div>
    @isset($jsPath)
        <script>{!! file_get_contents($jsPath) !!}</script>
    @endisset
    @isset($cssPath)
        <style>{!! file_get_contents($cssPath) !!}</style>
    @endisset

    <div
        x-data="LivewireUIModal()"
        x-on:close.stop="setShowPropertyTo(false)"
        x-on:keydown.escape.window="closeModalOnEscape()"
        {{-- x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
        x-on:keydown.shift.tab.prevent="prevFocusable().focus()" --}}
        x-show="show"
        {{-- x-trap.noscroll="show" --}}
        class="fixed inset-0 z-40 overflow-y-auto"
        style="display: none;"
    >
        <div
            x-show="show"
            x-on:click="closeModalOnClickAway()"
            x-transition:enter="ease-out duration-500 sm:duration-700"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in-out duration-500 sm:duration-700"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-all transform"
        >
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>

        <div class="fixed inset-0 overflow-hidden" x-show="show">
            <div class="absolute inset-0 overflow-hidden">
                <div 
                    class="fixed inset-y-0 right-0 flex max-w-full pointer-events-none" 
                    x-bind:class="{ 'pl-10 sm:pl-16' : !fullScreen}"
                    x-show="activeComponentType == 'slide-over'"
                >
                    <div 
                        x-show="show && activeComponentType == 'slide-over'" 
                        x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" 
                        x-transition:enter-start="translate-x-full" 
                        x-transition:enter-end="translate-x-0" 
                        x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" 
                        x-transition:leave-start="translate-x-0" 
                        x-transition:leave-end="translate-x-full" 
                        class="w-screen h-full pointer-events-auto" 
                        x-bind:class="modalWidth"
                        id="modal-container"
                        {{-- x-trap.noscroll.inert="show && showActiveComponent" --}}
                        x-description="Slide-over panel, show/hide based on slide-over state."
                    >
                        @foreach($components as $id => $component)
                            @if(isset($component['modalAttributes']['type']) && $component['modalAttributes']['type'] == 'slide-over')
                                <div class="flex flex-col h-full" x-show="activeComponent == '{{ $id }}'" x-ref="{{ $id }}" wire:key="{{ $id }}">
                                    @livewire($component['name'], $component['arguments'], key($id))
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="fixed inset-0 overflow-y-auto" x-show="activeComponentType == 'modal'">
                    <div class="flex items-end justify-center min-h-full p-4 text-center sm:items-center sm:p-0">
                        <div 
                            x-show="show && activeComponentType == 'modal'" 
                            x-transition:enter="ease-out duration-300" 
                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                            x-transition:leave="ease-in duration-200" 
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                            class="relative w-screen px-4 pt-5 pb-4 overflow-hidden text-left transition-all transform bg-white shadow-xl rounded-xl sm:p-6" 
                            x-bind:class="modalWidth" 
                            id="modal-container"
                            x-trap.noscroll.inert="show && showActiveComponent"
                            x-description="Modal panel, show/hide based on modal state." 
                        >
                            @foreach($components as $id => $component)
                                @if(isset($component['modalAttributes']['type']) && $component['modalAttributes']['type'] == 'modal')
                                    <div class="flex flex-col h-full" x-show="activeComponent == '{{ $id }}'" x-ref="{{ $id }}" wire:key="{{ $id }}">
                                        @livewire($component['name'], $component['arguments'], key($id))
                                    </div>
                                @endif
                            @endforeach
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>