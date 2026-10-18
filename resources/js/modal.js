window.LivewireUIModal = () => {
    return {
        show: false,
        showActiveComponent: true,
        activeComponent: false,
        componentHistory: [],
        visibleComponents: [],
        pendingReset: null,
        closedComponent: null,
        modalWidth: null ,
        listeners: [],
        getActiveComponentModalAttribute(key) {
            if (this.$wire.get('components')[this.activeComponent] !== undefined) {
                return this.$wire.get('components')[this.activeComponent]['modalAttributes'][key];
            }
        },
        closeModalOnEscape(trigger) {
            if (this.getActiveComponentModalAttribute('closeOnEscape') === false) {
                return;
            }

            if (!this.closingModal('closingModalOnEscape')) {
                return;
            }

            let force = this.getActiveComponentModalAttribute('closeOnEscapeIsForceful') === true;
            this.closeModal(force);
        },
        closeModalOnClickAway(trigger) {
            if (this.getActiveComponentModalAttribute('closeOnClickAway') === false) {
                return;
            }

            if (!this.closingModal('closingModalOnClickAway')) {
                return;
            }

            // Overlays can cancel their own layer without dismissing the entire workflow.
            const force = this.getActiveComponentModalAttribute('closeOnClickAwayIsForceful') !== false;
            this.closeModal(force);
        },
        closingModal(eventName) {
            const components = this.$wire.get('components');

            // Backdrop clicks can arrive during the leave transition after modal state has reset.
            if (this.activeComponent === false || !components?.[this.activeComponent]) {
                return false;
            }

            const componentName = components[this.activeComponent].name;

            var params = {
                id: this.activeComponent,
                closing: true,
            };

            Livewire.dispatchTo(componentName, eventName, params);

            return params.closing;
        },
        closeModal(force = false, skipPreviousModals = 0, destroySkipped = false) {
            if(this.show === false) {
                return;
            }

            if (this.getActiveComponentModalAttribute('dispatchCloseEvent') === true) {
                const componentName = this.$wire.get('components')[this.activeComponent].name;
                Livewire.dispatch('modalClosed', {name: componentName});
            }

            if (skipPreviousModals > 0) {
                for (var i = 0; i < skipPreviousModals; i++) {
                    const skipped = this.componentHistory.pop();
                    if (destroySkipped && skipped) {
                        Livewire.dispatch('destroyComponent', {id: skipped.id});
                    }
                }
            }

            const previous = this.componentHistory.pop();

            if (previous && !force) {
                this.setActiveModalComponent(previous.id, true, previous);
            } else {
                if (this.getActiveComponentModalAttribute('destroyOnClose') === true) {
                    Livewire.dispatch('destroyComponent', {id: this.activeComponent});
                }

                this.setShowPropertyTo(false);
            }
        },
        setActiveModalComponent(id, skip = false, previous = null) {
            this.setShowPropertyTo(true);

            if (this.activeComponent === id) {
                return;
            }

            if (this.activeComponent !== false && skip === false) {
                // Restore both the visible arrangement and the focused control when returning.
                this.componentHistory.push({
                    id: this.activeComponent,
                    visibleComponents: [...this.visibleComponents],
                    focus: document.activeElement,
                });
            }

            const replace = this.$wire.get('components')[id].modalAttributes.replace !== false;
            // Replacement changes only the top layer, preserving visible layers beneath it.
            const nextVisibleComponents = previous?.visibleComponents ?? [
                ...(replace ? this.visibleComponents.slice(0, -1) : this.visibleComponents)
                    .filter(componentId => componentId !== id),
                id,
            ];
            /**
             * Restore the previous layers before destruction removes the closing modal's backdrop.
             * Destroying during the leave animation would expose the page before its parent reappears.
             */
            const activate = () => {
                const closingComponent = this.activeComponent;
                const destroyOnClose = skip && this.getActiveComponentModalAttribute('destroyOnClose') === true;

                this.activeComponent = id;
                this.visibleComponents = nextVisibleComponents;
                this.showActiveComponent = true;
                this.modalWidth = this.getActiveComponentModalAttribute('maxWidthClass');

                if (destroyOnClose) {
                    /** Wait for Alpine to apply the restored layer's visibility before requesting removal. */
                    this.$nextTick(() => {
                        Livewire.dispatch('destroyComponent', {id: closingComponent});
                    });
                }
            };
            let focusableTimeout = 50;

            if (this.activeComponent === false || !replace || this.visibleComponents.includes(id)) {
                activate();
            } else {
                this.showActiveComponent = false;

                focusableTimeout = 400;

                setTimeout(activate, 300);
            }

            // A new opening owns the manager now; remove the component retained by a cancelled close.
            if (this.closedComponent && this.closedComponent !== id) {
                this.$wire.destroyComponent(this.closedComponent);
            }

            this.closedComponent = null;

            this.$nextTick(() => {
                let focusable = previous?.focus?.isConnected
                    ? previous.focus
                    : this.$refs[id]?.querySelector('[autofocus]');
                if (focusable) {
                    setTimeout(() => {
                        focusable.focus();
                    }, focusableTimeout);
                }
            });
        },
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])'

            return [...(this.$refs[this.activeComponent]?.querySelectorAll(selector) ?? [])]
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
        setShowPropertyTo(show) {
            this.show = show;

            if (show) {
                document.body.classList.add('overflow-y-hidden');
            } else {
                document.body.classList.remove('overflow-y-hidden');

                this.closedComponent = this.activeComponent;
                /** Reset after the leave animation, unless reopening cancels the old close first. */
                this.pendingReset = setTimeout(() => {
                    this.pendingReset = null;
                    this.closedComponent = null;
                    this.activeComponent = false;
                    // The next opening starts with fresh navigation and visible layers.
                    this.componentHistory = [];
                    this.visibleComponents = [];
                    this.$wire.resetState();
                }, 300);
            }
        },
        init() {
            this.modalWidth = this.getActiveComponentModalAttribute('maxWidthClass');

            this.listeners.push(
                /** Cancel the previous close before a new opening can be wiped by its delayed reset. */
                Livewire.on('openModal', () => {
                    // A pending close belongs to the previous navigation sequence, not the new modal.
                    if (this.pendingReset) {
                        clearTimeout(this.pendingReset);
                        this.pendingReset = null;
                        this.activeComponent = false;
                        this.componentHistory = [];
                        this.visibleComponents = [];
                    }
                })
            );

            this.listeners.push(
                Livewire.on('closeModal', (data) => {
                    this.closeModal(data?.force ?? false, data?.skipPreviousModals ?? 0, data?.destroySkipped ?? false);
                })
            );

            this.listeners.push(
                Livewire.on('activeModalComponentChanged', ({id}) => {
                    this.setActiveModalComponent(id);
                })
            );
        },
        destroy() {
            this.listeners.forEach((listener) => {
                listener();
            });
        }
    };
}
