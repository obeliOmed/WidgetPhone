/**
 * WidgetPhone — client-side blur feedback.
 *
 * Provides heuristic (not library-backed) blur feedback for phone number fields.
 * Full validation is always performed server-side via libphonenumber.
 *
 * Two states:
 *   ✓ (green) — passes heuristic check for the field's country
 *   ✗ (red)   — likely invalid (too short, wrong format)
 *
 * The heuristic errs on the side of green (fewer false ✗) to avoid frustrating
 * users with international numbers that don't match the country default.
 *
 * Attaches to any <input data-phone-validate> element.
 * Vanilla JS, ES5 compatible, no external dependencies.
 */
(function () {
    'use strict';

    /**
     * Strips formatting chars and checks the number length is plausible.
     * country is the ISO 3166-1 alpha-2 code from data-country attribute.
     */
    function isPlausiblePhone(value, country) {
        // Strip spaces, dashes, parentheses, dots
        var stripped = value.replace(/[\s\-\.\(\)]/g, '');

        if (!stripped) { return false; }

        // E.164 with international prefix: +[country code][number], total 8–15 digits
        if (/^\+[1-9]\d{6,14}$/.test(stripped)) { return true; }

        // Country-specific heuristics (without international prefix)
        switch ((country || 'ES').toUpperCase()) {
            case 'ES':
                // Spain: mobile 6/7, landline 8/9, emergency 1, total 9 digits
                return /^[6789]\d{8}$/.test(stripped) || /^[0-9]{9}$/.test(stripped);
            case 'FR':
                // France: 10 digits starting with 0
                return /^0[1-9]\d{8}$/.test(stripped);
            case 'GB':
                // UK: 10-11 digits starting with 0 or 7
                return /^(0[1-9]\d{8,9}|7\d{9})$/.test(stripped);
            case 'DE':
                // Germany: variable length, 6-12 digits (simplified)
                return /^\d{6,12}$/.test(stripped);
            default:
                // Generic: 6-15 digits
                return /^\d{6,15}$/.test(stripped);
        }
    }

    function clearFeedback(input) {
        var badge = input.parentNode.querySelector('.phone-feedback-badge');
        if (badge) { badge.parentNode.removeChild(badge); }
    }

    function showBadge(input, icon, cssClass) {
        clearFeedback(input);
        var badge = document.createElement('span');
        badge.className = 'phone-feedback-badge input-group-text ' + cssClass;
        badge.textContent = icon;
        badge.style.cssText = 'font-size:0.85rem;min-width:2.5rem;text-align:center;user-select:none;';
        input.insertAdjacentElement('afterend', badge);
    }

    function onBlur() {
        var value = this.value.trim();
        if (!value) { clearFeedback(this); return; }

        var country = this.getAttribute('data-country') || 'ES';

        if (isPlausiblePhone(value, country)) {
            showBadge(this, '✓', 'text-success');
        } else {
            showBadge(this, '✗', 'text-danger');
        }
    }

    function onReset() {
        var form = this;
        form.querySelectorAll('[data-phone-validate]').forEach(function (input) {
            clearFeedback(input);
        });
    }

    function initPhoneWidget() {
        document.querySelectorAll('input[data-phone-validate]').forEach(function (input) {
            input.addEventListener('blur', onBlur);
        });
        document.querySelectorAll('form').forEach(function (form) {
            form.addEventListener('reset', onReset);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPhoneWidget);
    } else {
        initPhoneWidget();
    }
})();
