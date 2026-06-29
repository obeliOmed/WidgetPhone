# 001 — feat(WidgetPhone): core implementation

**Branch**: `001-feat-phone-core-implementation`
**PR**: #1

## Summary

- **WHY**: FacturaScripts forms store phone numbers as raw strings with no normalization or validation. Consumer models cannot reject invalid numbers without duplicating the same regex across 10+ plugins.
- **WHAT**: `WidgetPhone` (BaseWidget subclass) + `PhoneValidator` (libphonenumber-backed) + `phone-widget.js` (vanilla JS blur feedback). Usage: `<widget type="phone" fieldname="phoneNumber" />`.
- **IMPACT**: Any plugin XMLView can use `type="phone"` to get E.164 normalization + international display + client-side ✓/✗ blur feedback. Server-side validation delegated to consumer model's `test()` via `PhoneValidator::validate()`.

## Files

| File | Description |
|---|---|
| `Lib/Widget/WidgetPhone.php` | BaseWidget subclass — type="phone", E.164 processFormData, international show() |
| `Lib/PhoneValidator.php` | validate() / normalize() / format() backed by giggsey/libphonenumber-for-php |
| `Assets/JS/phone-widget.js` | Vanilla JS blur feedback (✓ green / ✗ red), country heuristics, ES5 compatible |
| `Init.php` | Minimal lifecycle (no tables — auto-discovered by FS PluginsDeploy) |
| `facturascripts.ini` | version=260629.1, min_version=2024.1 |
| `composer.json` | require: giggsey/libphonenumber-for-php ^8.13 |

## Test plan

- [ ] Install WidgetPhone plugin on FacturaScripts (copy folder, Admin > Plugins > Enable)
- [ ] Add `<widget type="phone" fieldname="phoneNumber" />` to any XMLView
- [ ] Input `600123456` → saved as `+34600123456` (E.164)
- [ ] Display shows `+34 600 123 456` (international format)
- [ ] Blur feedback: `600123456` → ✓ green; `abc` → ✗ red
- [ ] Empty input → field stored as null (not empty string)
- [ ] `<widget type="phone" fieldname="phone" country="FR" />` → French numbers accepted without prefix

## Cross-plugin impact

N/A — standalone utility plugin, no schema changes.
