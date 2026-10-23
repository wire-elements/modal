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
            x-on:keydown.escape.window="show && closeModalOnEscape()"
            x-show="show"
            class="fixed inset-0 z-10 overflow-y-auto"
            style="display: none;"
    >
        {{-- Each layer keeps its backdrop and width; only the active layer accepts interaction. --}}
        {{-- Layers swap without a leave delay so replacement backdrops do not overlap and briefly darken the page. --}}
        @foreach($components as $id => $component)
            <div
                    x-show="visibleComponents.includes('{{ $id }}')"
                    x-bind:style="{ zIndex: visibleComponents.indexOf('{{ $id }}') }"
                    x-bind:inert="activeComponent !== '{{ $id }}'"
                    class="fixed inset-0 overflow-y-auto"
                    wire:key="modal-layer-{{ $id }}"
            >
                <div class="flex items-end justify-center min-h-dvh px-4 pt-4 pb-10 text-center sm:block sm:p-0">
                    <div
                            x-show="show"
                            x-on:click="activeComponent === '{{ $id }}' && closeModalOnClickAway()"
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

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    {{-- Hidden history panels must not re-enter when another component becomes active. --}}
                    <div
                            x-show="show && visibleComponents.includes('{{ $id }}') && (activeComponent !== '{{ $id }}' || showActiveComponent)"
                            x-transition:enter="ease-out duration-300"
                            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave="ease-in duration-200"
                            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                            class="relative inline-block w-full align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:w-full {{ $component['modalAttributes']['maxWidthClass'] }}"
                            x-bind:id="activeComponent === '{{ $id }}' ? 'modal-container' : null"
                            x-ref="{{ $id }}"
                            {{-- Navigation restores focus after activation; the trap must not choose a different control first. --}}
                            x-trap.noscroll.inert.noautofocus="show && showActiveComponent && activeComponent === '{{ $id }}'"
                            x-bind:aria-modal="activeComponent === '{{ $id }}' ? 'true' : null"
                            role="dialog"
                    >
                        @livewire($component['name'], $component['arguments'], key($id))
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
