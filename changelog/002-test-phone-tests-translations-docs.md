# 002 — test(WidgetPhone): tests, translations, and docs

**Branch**: `002-test-phone-tests-translations-docs`
**PR**: #2
**Sequential to**: PR #1 (`001-feat-phone-core-implementation`)

## Summary

- **WHY**: PR #1 has no tests or docs — Javi cannot merge a library widget without coverage proof and consumer guidance.
- **WHAT**: PHPUnit test suite (PhoneValidatorTest + WidgetPhoneTest) + 4 translation files (es_ES, en_EN, ca_ES, gl_ES) + README.md + LICENSE.
- **IMPACT**: `phpunit` runs standalone without a FacturaScripts installation. Consumer plugin developers have a README with copy-paste XMLView usage.

## Files

| File | Description |
|---|---|
| `Test/PhoneValidatorTest.php` | Unit tests for PhoneValidator (validate + normalize + format, ES/FR/GB/international cases) |
| `Test/WidgetPhoneTest.php` | Unit tests for WidgetPhone (inputHtml attributes, processFormData E.164, country normalization) |
| `Translation/es_ES.json` | Spanish translations |
| `Translation/en_EN.json` | English translations |
| `Translation/ca_ES.json` | Catalan translations |
| `Translation/gl_ES.json` | Galician translations |
| `README.md` | Installation, XMLView usage examples, model::test() integration pattern |
| `LICENSE` | GPL-3.0-only |

## Test plan

- [ ] `composer install && ./vendor/bin/phpunit` → all tests green (no FS installation needed)
- [ ] PhoneValidatorTest covers: valid ES mobile, valid ES landline, international with prefix, invalid (too short, all zeros, non-numeric)
- [ ] WidgetPhoneTest covers: type="tel" in HTML, data-country attribute, null on empty, E.164 storage

## Cross-plugin impact

N/A
