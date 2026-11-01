/**
 * SAMSARA SIMS - Currency Formatting & Masking Module
 * Indonesian Rupiah (IDR) display formatting (thousands separator: dot '.')
 * Ensures clean numeric separation between display presentation and underlying model/data.
 */

/**
 * Format any numeric value or digit string into Indonesian currency display.
 * E.g., 500000 -> "500.000", 1000000 -> "1.000.000"
 * 
 * @param {number|string} value - Raw number or string of digits
 * @param {boolean} withPrefix - Whether to prepend 'Rp '
 * @returns {string} Formatted display string
 */
export function formatRupiah(value, withPrefix = false) {
    if (value === null || value === undefined || value === '') {
        return withPrefix ? 'Rp 0' : '';
    }
    const cleanDigits = String(value).replace(/\D/g, '');
    if (!cleanDigits) {
        return withPrefix ? 'Rp 0' : '';
    }
    // Remove leading zeros while keeping single '0'
    const trimmed = cleanDigits.replace(/^0+(?=\d)/, '');
    const formatted = (trimmed === '' ? '0' : trimmed).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return withPrefix ? `Rp ${formatted}` : formatted;
}

/**
 * Parse an Indonesian formatted currency string back into a pure numeric integer.
 * E.g., "500.000" or "Rp 500.000" -> 500000
 * 
 * @param {string|number} value
 * @returns {number} Pure integer
 */
export function parseRupiah(value) {
    if (value === null || value === undefined || value === '') return 0;
    const cleanDigits = String(value).replace(/\D/g, '');
    return cleanDigits ? parseInt(cleanDigits, 10) : 0;
}

/**
 * Register the custom Alpine.js directive: x-currency
 * 
 * Usage examples:
 *  1. Two-way bound to Alpine state:
 *     <input type="text" x-currency="openingCashInput">
 * 
 *  2. Standalone HTML form input:
 *     <input type="text" name="opening_cash" value="500000" x-currency>
 */
export function registerCurrencyDirective(Alpine) {
    Alpine.directive('currency', (el, { expression, modifiers }, { effect, cleanup }) => {
        // Enforce numeric keypad on virtual mobile keyboards
        if (!el.hasAttribute('inputmode')) {
            el.setAttribute('inputmode', 'numeric');
        }

        // Locate or create hidden input for standard HTML form submissions
        let hiddenInput = null;
        if (el.parentElement) {
            hiddenInput = el.parentElement.querySelector('input[type="hidden"][data-currency-hidden]');
            if (!hiddenInput && el.hasAttribute('name')) {
                const fieldName = el.getAttribute('name');
                hiddenInput = el.parentElement.querySelector(`input[type="hidden"][name="${fieldName}"]`);
                if (!hiddenInput) {
                    hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = fieldName;
                    hiddenInput.setAttribute('data-currency-hidden', '');
                    el.parentElement.appendChild(hiddenInput);
                }
                el.removeAttribute('name');
            }
        }

        const syncHiddenInput = (rawVal) => {
            if (hiddenInput) {
                hiddenInput.value = (rawVal !== '' && rawVal !== null && rawVal !== undefined) ? rawVal : '';
            }
        };

        let isInternalUpdate = false;

        // Masking handler on user typing
        const handleInput = () => {
            const currentStr = el.value;
            const cursorPos = el.selectionStart || 0;
            const digitsBeforeCursor = currentStr.slice(0, cursorPos).replace(/\D/g, '').length;

            const formatted = formatRupiah(currentStr);
            const raw = parseRupiah(currentStr);

            el.value = formatted;

            // Maintain cursor position relative to numeric digits
            let newCursorPos = 0;
            let digitsSeen = 0;
            for (let i = 0; i < formatted.length; i++) {
                if (/\d/.test(formatted[i])) {
                    digitsSeen++;
                }
                if (digitsSeen === digitsBeforeCursor) {
                    newCursorPos = i + 1;
                    break;
                }
            }
            if (digitsBeforeCursor === 0) newCursorPos = 0;
            try {
                el.setSelectionRange(newCursorPos, newCursorPos);
            } catch (_) {}

            syncHiddenInput(raw);

            // Update parent Alpine model if an expression is bound
            if (expression) {
                isInternalUpdate = true;
                try {
                    Alpine.evaluate(el, `${expression} = ${raw}`);
                } catch (e) {
                    console.warn('[x-currency] Evaluation failed:', e);
                }
                Alpine.nextTick(() => {
                    isInternalUpdate = false;
                });
            }

            el.dispatchEvent(new CustomEvent('currency-change', {
                bubbles: true,
                detail: { raw, formatted }
            }));
        };

        el.addEventListener('input', handleInput);
        cleanup(() => el.removeEventListener('input', handleInput));

        // Reactive effect when Alpine expression is updated externally (e.g. preset chips or reset)
        if (expression) {
            const evaluate = Alpine.evaluateLater(el, expression);
            effect(() => {
                evaluate((value) => {
                    if (isInternalUpdate) return;
                    const formatted = formatRupiah(value);
                    if (el.value !== formatted) {
                        el.value = formatted;
                    }
                    syncHiddenInput(value);
                });
            });
        } else {
            // Initial formatting for static or standard forms
            const initial = el.value || el.getAttribute('value') || '';
            if (initial !== '') {
                const formatted = formatRupiah(initial);
                el.value = formatted;
                syncHiddenInput(parseRupiah(initial));
            }
        }
    });
}
