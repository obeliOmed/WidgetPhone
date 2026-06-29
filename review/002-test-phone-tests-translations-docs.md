# Review — PR #2 test(WidgetPhone): tests, translations, and docs

**Reviewer**: Javi
**Branch**: `002-test-phone-tests-translations-docs`
**Base**: `001-feat-phone-core-implementation`

## Pre-merge checklist

- [ ] PR #1 already merged (this branch builds on top of it)
- [ ] 4 translation files present (es_ES, en_EN, ca_ES, gl_ES)
- [ ] README.md present with usage examples
- [ ] LICENSE present (GPL-3.0-only)

## Test run (standalone — no FacturaScripts needed)

```bash
cd Plugins/WidgetPhone
composer install
./vendor/bin/phpunit
```

Expected output: all tests GREEN, 0 failures.

## Expected coverage

- [ ] PhoneValidatorTest: valid ES mobile ✓, valid ES landline ✓, E.164 with prefix ✓, invalid (zeros/short/alpha) → false ✓
- [ ] WidgetPhoneTest: inputHtml has type="tel" ✓, data-country attribute ✓, processFormData stores E.164 ✓, empty → null ✓

## PASS / FAIL

☐ PASS &nbsp;&nbsp; ☐ FAIL

**Notes**:
