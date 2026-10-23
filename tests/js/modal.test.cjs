const assert = require('node:assert/strict');
const fs = require('node:fs');
const path = require('node:path');
const vm = require('node:vm');
const test = require('node:test');

const modalSource = fs.readFileSync(
    path.resolve(__dirname, '../../resources/js/modal.js'),
    'utf8'
);

function createModal() {
    let nextTimerId = 0;
    const timers = new Map();
    const cancelledTimers = new Set();
    const context = {
        window: {},
        document: { body: { classList: { add() {}, remove() {} } } },
        setTimeout(callback, delay) {
            const id = ++nextTimerId;
            timers.set(id, { callback, delay });
            return id;
        },
        clearTimeout(id) {
            cancelledTimers.add(id);
        },
    };

    vm.runInNewContext(modalSource, context, { timeout: 1000 });

    return {
        modal: context.window.LivewireUIModal(),
        timers,
        cancelledTimers,
    };
}

test('closing an old modal does not reset a modal opened before cleanup finishes', () => {
    const { modal, timers, cancelledTimers } = createModal();
    let resetCount = 0;
    modal.$wire = { resetState() { resetCount++; } };

    modal.setShowPropertyTo(false);
    const closeTimerId = modal.resetStateTimeout;
    modal.setShowPropertyTo(true);
    modal.activeComponent = 'new-modal';

    assert.equal(timers.get(closeTimerId).delay, 300);
    assert.equal(cancelledTimers.has(closeTimerId), true);
    assert.equal(resetCount, 0);
    assert.equal(modal.activeComponent, 'new-modal');
});

test('a closed modal still resets after the close cleanup delay', () => {
    const { modal, timers } = createModal();
    let resetCount = 0;
    modal.$wire = { resetState() { resetCount++; } };

    modal.setShowPropertyTo(false);
    const closeTimerId = modal.resetStateTimeout;
    timers.get(closeTimerId).callback();

    assert.equal(resetCount, 1);
    assert.equal(modal.activeComponent, false);
    assert.equal(modal.resetStateTimeout, null);
});
