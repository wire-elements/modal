window.LivewireUiModal = () => {
    return {
        show: false,
        showActiveComponent: true,
        activeComponent: false,
        componentHistory: [],
        modalWidth: 'sm:max-w-2xl',
        modalTitle: '',
        getActiveComponentModalAttribute(key) {
            if(this.$wire.get('components')[this.activeComponent] !== undefined) {
                return this.$wire.get('components')[this.activeComponent]['modalAttributes'][key];
            }
        },
        closeModalOnEscape(trigger) {
            if(this.getActiveComponentModalAttribute('closeOnEscape') === false) {
                return;
            }

            let force = this.getActiveComponentModalAttribute('closeOnEscapeIsForceful') === true;
            this.closeModal(force);
        },
        closeModalOnClickAway(trigger) {
            if(this.getActiveComponentModalAttribute('closeOnClickAway') === false) {
                return;
            }

            this.closeModal(true);
        },
        closeModal(force = false, skipPreviousModals = 0) {

            if(this.getActiveComponentModalAttribute('dispatchCloseEvent') === true) {
                const componentName = this.$wire.get('components')[this.activeComponent].name;
                Livewire.emit('modalClosed', componentName);
            }

            if (skipPreviousModals > 0) {
                for ( var i = 0; i < skipPreviousModals; i++ ) {
                    this.componentHistory.pop();
                }
            }

            const id = this.componentHistory.pop();

            if (id && force === false) {
                this.setActiveModalComponent(id, true);
            }

            if (this.isBootstrap()) {
                this.bsCloseModal(this.activeComponent)

                return;
            }

            this.show = false;
        },
        setActiveModalComponent(id, skip = false) {
            this.show = true;

            if (this.activeComponent !== false && skip === false) {
                this.componentHistory.push(this.activeComponent);
            }

            let focusableTimeout = 50;

            if (this.activeComponent === false) {

                this.componentAttributes(id)

            } else {
                this.showActiveComponent = false;

                focusableTimeout = 400;

                setTimeout(() => {
                    this.componentAttributes(id)
                }, 300);
            }

            this.$nextTick(() => {
                let focusable = this.$refs[id].querySelector('[autofocus]');
                if (focusable) {
                    setTimeout(() => { focusable.focus(); }, focusableTimeout);
                }
            });

            if (this.isBootstrap()) {
                this.modalTitle = this.getActiveComponentModalAttribute('bsTitle');

                this.bsOpenModal(id)

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
                this.closeModal(force, skipPreviousModals);
            });

            Livewire.on('activeModalComponentChanged', (id) => {
                this.setActiveModalComponent(id);
            });
        },
        componentAttributes(id) {
            this.activeComponent = id
            this.showActiveComponent = true;
            this.modalWidth = 'sm:max-w-' + this.getActiveComponentModalAttribute('maxWidth');
            if (this.isBootstrap()) {
                this.modalWidth = this.bsModalWidth();
            }
        },
        isBootstrap() {
            return this.getActiveComponentModalAttribute('framework') === 'bootstrap';
        },
        bsModalWidth() {
            const width = this.getActiveComponentModalAttribute('bsWidth');
            if (width !== '') {
                return 'modal-' +width;
            }
        },
        bsModal(modal) {
            return document.getElementById(modal);
        },
        bsCloseModal(modal) {
            const backdrop = document.querySelector('.modal-backdrop.fade.show');

            this.bsModal(modal).setAttribute('aria-hidden', 'true');
            backdrop.classList.remove('show');

            setTimeout(() => {
                this.bsModal(modal).classList.remove('show');
            });

            setTimeout(() => {
                this.bsModal(modal).style.display = 'none';
                backdrop.remove();
            }, 500);
        },
        bsOpenModal(modal) {
            const backdrop = document.createElement('div');
            backdrop.classList.add('modal-backdrop', 'fade');

            document.body.classList.add('modal-open');
            document.body.appendChild(backdrop);

            this.bsModal(modal).style.display = 'block';
            this.bsModal(modal).setAttribute('aria-hidden', 'false', 'show');

            setTimeout(() => {
                this.bsModal(modal).classList.add('show');
                backdrop.classList.add('show');
            });
        }
    };
}
