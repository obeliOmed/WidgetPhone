# WidgetPhone — Documentación técnica

## Arquitectura

```
Lib/Widget/WidgetPhone.php   extends BaseWidget — type="phone", atributo XML "country" (default ES)
Lib/PhoneValidator.php       wrapper sobre giggsey/libphonenumber-for-php
Assets/JS/phone-widget.js    heurística regex por país — NO usa libphonenumber en cliente (demasiado pesado)
vendor/                      giggsey/libphonenumber-for-php empaquetado en el propio plugin
```

## ⚠️ Por qué el plugin trae su propio `vendor/`

FacturaScripts usa **un único `vendor/` compartido en la raíz de la instalación** y **nunca fusiona** el `composer.json` de un plugin en él. La mayoría de administradores de tienda FS no ejecutan `composer` manualmente. Por eso este plugin **empaqueta su propio `vendor/`** (solo dependencias de producción, `composer install --no-dev -o`, ~19MB) directamente en el repo.

`Init::init()` (se ejecuta en cada request) comprueba si `libphonenumber\PhoneNumberUtil` ya está cargada y, si no, hace `require_once __DIR__ . '/vendor/autoload.php'`. Esto garantiza que el plugin funciona nada más copiarlo a `Plugins/WidgetPhone/`, sin pasos de instalación adicionales.

**Historial del bug** (para referencia futura si se repite algo parecido): la primera versión asumía que el admin ejecutaría `composer require giggsey/libphonenumber-for-php` en la raíz de FS (ver PR #3) — en producción esto nunca se ejecutó y el error `Class "libphonenumber\PhoneNumberUtil" not found` apareció en cualquier página que renderizara el widget. Fix definitivo: PR #5 (vendor bundled).

## Flujo de datos

1. **Cliente (JS)**: heurística ligera por país (regex simple, ej. España móvil `/^[6789]\d{8}$/`) + aceptación de formato E.164 genérico (`/^\+[1-9]\d{6,14}$/`). Es solo feedback visual — no usa la librería completa (demasiado peso para el navegador).
2. **Servidor (PHP)**: `WidgetPhone::processFormData()` llama a `PhoneValidator::normalize()`. Si el número es válido → se guarda en formato **E.164** (`+34600123456`, sin espacios). Si no es parseable → se guarda el valor tal cual escrito (para que `model::test()` del modelo consumidor pueda rechazarlo si hace su propia validación).
3. **Display**: `WidgetPhone::show()` llama a `PhoneValidator::format()` → formato internacional legible (`+34 600 12 34 56`). El formato exacto (agrupación de dígitos) lo decide `libphonenumber` según las reglas de cada país — no es configurable manualmente.

## `PhoneValidator` — 3 métodos

```php
validate(string $phone, string $defaultCountry = 'ES'): bool
    // PhoneNumberUtil::parse() + isValidNumber()
    // $defaultCountry se usa SOLO si el número no lleva prefijo internacional

normalize(string $phone, string $defaultCountry = 'ES'): ?string
    // parse() + isValidNumber() + format(E164) — null si no es válido/parseable

format(string $phone, string $defaultCountry = 'ES'): ?string
    // parse() + format(INTERNATIONAL) — null si no es parseable
    // NO valida — solo formatea (úsalo solo con números ya normalizados)
```

Todas capturan `NumberParseException` internamente — nunca lanzan excepción hacia el widget.

## Por qué E.164 para storage

Formato directo compatible con WhatsApp Business API (ObelioComms lo usa sin transformación adicional) y con la mayoría de pasarelas SMS/email transaccional.

## Configuración de país por campo

```xml
<widget type="phone" fieldname="phoneNumber" />              <!-- ES por defecto -->
<widget type="phone" fieldname="phoneMobile" country="FR" /> <!-- Francia -->
```

## Limitaciones conocidas

- Formato de display (agrupación de dígitos) depende de `libphonenumber` — no personalizable desde el widget.
- **El validador JS es heurístico y puede discrepar del PHP real (`libphonenumber`)** — no es un caso raro, es estructural: el JS usa un regex simple por país (`/^(\+[1-9]\d{6,14}|[6789]\d{8}|9\d{8})$/` para ES), sin conocimiento real de rangos de numeración. Ejemplo concreto: un usuario escribe un móvil de Reino Unido sin prefijo (`07911123456`) en un campo configurado con `country="ES"`. El regex heurístico puede darlo por válido o inválido según casualidad de forma, pero `libphonenumber` en el servidor lo evaluará correctamente como número inválido para España (le falta el prefijo `+44`) — el aviso visual puede mentir. Esto es aceptado como trade-off de rendimiento (no cargar 3MB+ de `libphonenumber` en el navegador) — la validación real y autoritativa es siempre la del servidor.
- `vendor/` committeado aumenta el peso del repo (~19MB) — es una decisión consciente frente al riesgo de que el admin nunca ejecute composer.

## Tests

`Test/PhoneValidatorTest.php` — 32 casos: números ES válidos/inválidos (móvil/fijo, con/sin prefijo, espacios/guiones), internacionales (UK/US/FR), normalización a E.164, formato internacional, país case-insensitive. Ejecutar: `phpunit --testsuite "Validator (standalone)"`.
