# WidgetPhone — International phone number validator for FacturaScripts

A free FacturaScripts community plugin that adds a `type="phone"` form widget backed by
[Google libphonenumber](https://github.com/giggsey/libphonenumber-for-php).

---

## Features

- **Validation** — powered by `giggsey/libphonenumber-for-php` (covers 250+ countries)
- **E.164 storage** — `+34600123456` — direct compatibility with WhatsApp Business API
- **International display** — `show()` returns `+34 600 123 456` (human-readable)
- **Auto-normalization** — bare number `600123456` → stored as `+34600123456`
- **Country configuration** — default `ES`, overridable per field via XML attribute
- **Client-side feedback** — ✓ / ✗ badge on blur (heuristic, no bundle overhead)

---

## Installation

FacturaScripts uses a single shared `vendor/` at the installation root and never merges a
plugin's `composer.json` dependencies into it. To avoid requiring a manual `composer
require` step (which most FS shop admins won't run), this plugin ships its own `vendor/`
committed to the repo — `giggsey/libphonenumber-for-php` is loaded directly from
`Plugins/WidgetPhone/vendor/autoload.php` in `Init::init()` on every request.

1. Copy the plugin to `Plugins/WidgetPhone/` inside your FacturaScripts installation
   (`vendor/` included — do not delete it).
2. Go to **Admin → Plugins** and install **WidgetPhone**.

No composer step required — it works out of the box.

---

## Usage in XMLView

```xml
<!-- Default country ES (Spain) -->
<column name="phoneNumber" numcolumns="4" title="Teléfono">
    <widget type="phone" fieldname="phoneNumber" />
</column>

<!-- Custom country (France) -->
<column name="phoneMobile" numcolumns="4" title="Móvil">
    <widget type="phone" fieldname="phoneMobile" country="FR" />
</column>
```

## Server-side validation in your model

```php
use FacturaScripts\Plugins\WidgetPhone\Lib\PhoneValidator;

public function test(): bool
{
    if (!empty($this->phoneNumber) && !PhoneValidator::validate($this->phoneNumber)) {
        $this->toolBox()->log()->error('invalid-phone');
        return false;
    }
    return parent::test();
}
```

---

## Number format examples

| Input (user types) | Stored (E.164) | Displayed (show()) |
|---|---|---|
| `600123456` | `+34600123456` | `+34 600 12 34 56` |
| `+34 600 123 456` | `+34600123456` | `+34 600 12 34 56` |
| `0034600123456` | `+34600123456` | `+34 600 12 34 56` |
| `0612345678` (FR) | `+33612345678` | `+33 6 12 34 56 78` |

---

## Requirements

- FacturaScripts 2024.1 or higher
- PHP 8.0+
- `giggsey/libphonenumber-for-php` ^8.13 (installed via Composer)

---

## License

GPL-3.0-only — see [LICENSE](LICENSE).

---

## Credits

Built by the **[obeliOmed](https://obeliomed.com)** team — open-source SaaS for medical clinics on FacturaScripts.

Contributions welcome via GitHub issues and pull requests.
