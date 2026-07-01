# Review — PR #1 feat(WidgetPhone): core implementation

**Reviewer**: Javi
**Branch**: `001-feat-phone-core-implementation`
**Base**: `develop`

## Pre-merge checklist

- [ ] `facturascripts.ini` version = `260629.1` (CalVer ADR-028)
- [ ] Branch name follows ADR-031 (`NNN-type-scope-slug`)
- [ ] No FacturaScripts core files modified
- [ ] `composer.json` dependency `giggsey/libphonenumber-for-php ^8.13` correct

## Smoke test (FacturaScripts XAMPP install)

1. Copy `WidgetPhone/` into `Plugins/WidgetPhone/`
2. Admin > Plugins > Enable `WidgetPhone`
3. Open any Edit* controller XMLView — add `<widget type="phone" fieldname="phoneNumber" />`
4. Enter `600123456` → save → verify DB stores `+34600123456`
5. Reload page → field shows `+34 600 123 456`
6. Enter `abc` → blur → red ✗ badge appears next to input
7. Enter `600123456` → blur → green ✓ badge
8. Clear field → save → verify DB stores NULL

## Expected result

✓ Widget renders input type="tel"  
✓ E.164 stored on save  
✓ International format displayed on read  
✓ Blur feedback visible  
✓ Empty → NULL (not empty string)  
✓ No JS errors in browser console

## PASS / FAIL

☐ PASS &nbsp;&nbsp; ☐ FAIL

**Notes**:
