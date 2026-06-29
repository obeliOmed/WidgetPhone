<?php
declare(strict_types=1);

namespace FacturaScripts\Plugins\WidgetPhone\Lib;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

/**
 * Phone number validator backed by Google libphonenumber (giggsey/libphonenumber-for-php).
 *
 * Handles:
 * - Validation:   is the number valid for the given country context?
 * - Normalization: any common format → E.164 ("+34600123456") for storage.
 * - Formatting:   E.164 stored value → international display ("+34 600 123 456").
 *
 * $defaultCountry is an ISO 3166-1 alpha-2 code ('ES', 'FR', 'GB', 'US', …).
 * It is only used as fallback when the number has no international prefix.
 * Numbers with "+" prefix are parsed globally regardless of $defaultCountry.
 *
 * Usage in model::test():
 *   if (!empty($this->phone) && !PhoneValidator::validate($this->phone)) {
 *       $this->toolBox()->log()->error('invalid-phone');
 *       return false;
 *   }
 */
class PhoneValidator
{
    /**
     * Returns true when the number parses and passes libphonenumber's isValidNumber() check.
     */
    public static function validate(string $phone, string $defaultCountry = 'ES'): bool
    {
        if (trim($phone) === '') {
            return false;
        }

        $util = PhoneNumberUtil::getInstance();

        try {
            $parsed = $util->parse($phone, strtoupper($defaultCountry));
            return $util->isValidNumber($parsed);
        } catch (NumberParseException) {
            return false;
        }
    }

    /**
     * Normalizes any parseable phone number to E.164 format ("+34600123456").
     * Returns null when the number cannot be parsed or is invalid.
     * Stores null so model::test() can reject the field if required.
     */
    public static function normalize(string $phone, string $defaultCountry = 'ES'): ?string
    {
        if (trim($phone) === '') {
            return null;
        }

        $util = PhoneNumberUtil::getInstance();

        try {
            $parsed = $util->parse($phone, strtoupper($defaultCountry));
            if (!$util->isValidNumber($parsed)) {
                return null;
            }
            return $util->format($parsed, PhoneNumberFormat::E164);
        } catch (NumberParseException) {
            return null;
        }
    }

    /**
     * Formats a stored E.164 or any parseable number for display.
     * Returns the international format ("+34 600 123 456") — spaces included for readability.
     * Returns null when the number cannot be parsed.
     */
    public static function format(string $phone, string $defaultCountry = 'ES'): ?string
    {
        if (trim($phone) === '') {
            return null;
        }

        $util = PhoneNumberUtil::getInstance();

        try {
            $parsed = $util->parse($phone, strtoupper($defaultCountry));
            return $util->format($parsed, PhoneNumberFormat::INTERNATIONAL);
        } catch (NumberParseException) {
            return null;
        }
    }
}
