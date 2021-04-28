window.LivewireUiModal = () => {
    return {
        show: false,
        showActiveComponent: true,
        activeComponent: false,
        componentHistory: [],
        modalWidth: 'sm:max-w-2xl',
        getActiveComponentModalAttribute(key) {
            return this.$wire.get('components')[this.activeComponent]['modalAttributes'][key];
        },
        closeModalViaEscape(trigger) {
            if(this.getActiveComponentModalAttribute('closeOnEscape') === false) {
                return;
            }

            this.show = false;
        },
        closeModalViaClickAway(trigger) {
            if(this.getActiveComponentModalAttribute('closeOnClickAway') === false) {
                return;
            }

            this.show = false;
        },
        setActiveModalComponent(id, skip = false) {
            this.show = true;

            if (this.activeComponent !== false && skip === false) {
                this.componentHistory.push(this.activeComponent);
            }

            if (this.activeComponent === false) {
                this.activeComponent = id
                this.showActiveComponent = true;
                this.modalWidth = 'sm:max-w-' + this.getActiveComponentModalAttribute('maxWidth');
            } else {
                this.showActiveComponent = false;

                setTimeout(() => {
                    this.activeComponent = id;
                    this.showActiveComponent = true;
                    this.modalWidth = 'sm:max-w-' +  this.getActiveComponentModalAttribute('maxWidth');
                }, 300);
            }

        },
        focusables() {
            let selector = 'a, button, input, textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'

            return [...this.$el.querySelectorAll(selector)]
                .filter(el => !el.hasAttribute('disabled'))
        },
        firstFocusable() {
            return this.focusables()[0]
        },
        lastFocusable() {
            return this.focusables().slice(-1)[0]
        },
        nextFocusable() {
            return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable()
        },
        prevFocusable() {
            return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable()
        },
        nextFocusableIndex() {
            return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1)
        },
        prevFocusableIndex() {
            return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1
        },
        init() {
            this.$watch('show', value => {
                if (value) {
                    document.body.classList.add('overflow-y-hidden');
                } else {
                    document.body.classList.remove('overflow-y-hidden');

                    setTimeout(() => {
                        this.activeComponent = false;
                        this.$wire.resetState();
                    }, 300);
                }
            });

            Livewire.on('closeModal', (force = false, skipPreviousModals = 0) => {

                if (skipPreviousModals > 0) {
                    for ( var i = 0; i < skipPreviousModals; i++ ) {
                        this.componentHistory.pop();
                    }
                }

                const id = this.componentHistory.pop();

                if (id && force === false) {
                    if (id) {
                        this.setActiveModalComponent(id, true);
                    } else {
                        this.show = false;
                    }
                } else {
                    this.show = false;
                }
            });

            Livewire.on('activeModalComponentChanged', (id) => {
                this.setActiveModalComponent(id);
            });
        }
    };
}
