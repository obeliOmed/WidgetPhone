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

FacturaScripts uses a single shared `vendor/` at the installation root — plugins do NOT
carry their own `vendor/`. This plugin declares `giggsey/libphonenumber-for-php` in its
`composer.json`, but you must install it at the **FacturaScripts root**, not inside the
plugin folder.

1. Copy the plugin to `Plugins/WidgetPhone/` inside your FacturaScripts installation.
2. Go to **Admin → Plugins** and install **WidgetPhone**.
3. From the **FacturaScripts root** (NOT `Plugins/WidgetPhone/`), run:
   ```
   composer require giggsey/libphonenumber-for-php
   ```
   (or use the "update dependencies" action in the Plugins admin screen, if your
   FacturaScripts version exposes it — it runs the equivalent `composer require` at root).

Without this step, any page rendering a `type="phone"` widget will throw
`Class "libphonenumber\PhoneNumberUtil" not found"`.

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
