<div
    x-data="LivewireUiModal()"
    x-init="init()"
    x-on:keydown.escape.window="closeModalOnEscape()"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
>
    @forelse($components as $id => $component)
        <div
            id="{{ $id }}"
            class="modal fade" tabindex="-1" role="dialog"
            aria-hidden="true"
        >
            <div class="modal-dialog" x-bind:class="modalWidth" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" x-text="modalTitle"></h5>
                        <span x-on:click="bsCloseModal('{{ $id }}')" style="cursor: pointer">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor"
                                     class="bi bi-x" viewBox="0 0 16 16">
                                  <path
                                      d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"></path>
                                </svg>
                            </span>
                    </div>
                    <div class="modal-body">
                        @livewire($component['name'], $component['attributes'], key($id))
                    </div>
                </div>
            </div>
        </div>
    @empty
    @endforelse
</div>
