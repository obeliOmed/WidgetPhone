<?php
declare(strict_types=1);

namespace FacturaScripts\Plugins\WidgetPhone\Lib\Widget;

use FacturaScripts\Core\Lib\AssetManager;
use FacturaScripts\Core\Lib\Widget\BaseWidget;
use FacturaScripts\Core\Tools;
use FacturaScripts\Plugins\WidgetPhone\Lib\PhoneValidator;

/**
 * Phone number input widget for FacturaScripts XMLView forms.
 *
 * Usage in XMLView (default country ES):
 *   <widget type="phone" fieldname="phoneNumber" />
 *
 * Custom country (e.g. France):
 *   <widget type="phone" fieldname="phoneNumber" country="FR" />
 *
 * Provides:
 * - HTML5 type="tel" input with autocomplete="tel"
 * - Client-side heuristic blur feedback (✓ / ✗) via phone-widget.js
 * - processFormData(): normalizes to E.164 via libphonenumber; stores raw if unparseable
 * - show(): displays stored E.164 as international format (+34 600 123 456)
 *
 * Validation is left to the model's test() method via PhoneValidator::validate().
 */
class WidgetPhone extends BaseWidget
{
    /**
     * ISO 3166-1 alpha-2 country code used as default region when parsing numbers
     * that have no international prefix. Populated from the 'country' XML attribute.
     *
     * BaseWidget auto-sets public properties from $data, so <widget country="FR" />
     * sets $this->country = 'FR' before our constructor normalization runs.
     */
    public string $country = 'ES';

    public function __construct($data)
    {
        parent::__construct($data);
        // Normalize country code to uppercase regardless of XML casing.
        $this->country = strtoupper($this->country);
    }

    /** Registers phone-widget.js for client-side blur feedback. */
    protected function assets(): void
    {
        $route = Tools::config('route');
        AssetManager::addJs($route . '/Plugins/WidgetPhone/Assets/JS/phone-widget.js');
    }

    /**
     * Renders the tel input wrapped in an input-group div.
     * Passes country code to JS via data-country attribute.
     */
    protected function inputHtml($type = 'tel', $extraClass = ''): string
    {
        $class = $this->combineClasses($this->css('form-control'), $this->class, $extraClass);
        $value = $this->escapeHtml((string)($this->value ?? ''));

        $input = '<input type="tel"'
            . ' name="' . $this->fieldname . '"'
            . ' value="' . $value . '"'
            . ' class="' . $class . '"'
            . ' autocomplete="tel"'
            . ' data-phone-validate="1"'
            . ' data-country="' . $this->escapeHtml($this->country) . '"'
            . $this->inputHtmlExtraParams()
            . '/>';

        return '<div class="input-group">' . $input . '</div>';
    }

    /**
     * Normalizes to E.164 via libphonenumber.
     * If the value cannot be parsed, stores the raw string so model::test() can reject it.
     * Empty input sets the field to null.
     */
    public function processFormData(&$model, $request): void
    {
        $raw = trim((string)$request->request->get($this->fieldname, ''));

        if ($raw === '') {
            $model->{$this->fieldname} = null;
            return;
        }

        $normalized = PhoneValidator::normalize($raw, $this->country);
        // Store E.164 if parseable, raw string if not (model::test() rejects invalid value).
        $model->{$this->fieldname} = $normalized ?? $raw;
    }

    /**
     * Displays stored value as international format (+34 600 123 456).
     * Falls back to the raw stored value when formatting fails.
     */
    protected function show(string $type = 'html'): string
    {
        if (empty($this->value)) {
            return '';
        }

        $formatted = PhoneValidator::format((string)$this->value, $this->country);
        return $this->escapeHtml($formatted ?? (string)$this->value);
    }
}
