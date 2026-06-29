<?php
declare(strict_types=1);

namespace FacturaScripts\Plugins\WidgetPhone\Test;

use FacturaScripts\Plugins\WidgetPhone\Lib\PhoneValidator;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for PhoneValidator.
 *
 * Requires giggsey/libphonenumber-for-php (installed via composer) but NOT FacturaScripts.
 * Run standalone: phpunit --testsuite "Validator (standalone)"
 */
class PhoneValidatorTest extends TestCase
{
    // -------------------------------------------------------------------------
    // validate() — Spanish numbers
    // -------------------------------------------------------------------------

    /** @dataProvider validSpanishProvider */
    public function testValidSpanishNumbers(string $phone): void
    {
        $this->assertTrue(PhoneValidator::validate($phone, 'ES'), "Should be valid (ES): $phone");
    }

    public static function validSpanishProvider(): array
    {
        return [
            ['+34600123456'],   // mobile E.164
            ['600123456'],      // mobile without prefix (country ES inferred)
            ['+34 600 123 456'], // mobile with spaces
            ['600 123 456'],    // mobile with spaces, no prefix
            ['600-123-456'],    // mobile with dashes
            ['+34912345678'],   // Madrid landline E.164
            ['912345678'],      // Madrid landline without prefix
            ['+34971123456'],   // Balearic Islands landline
        ];
    }

    /** @dataProvider invalidSpanishProvider */
    public function testInvalidSpanishNumbers(string $phone, string $reason): void
    {
        $this->assertFalse(PhoneValidator::validate($phone, 'ES'), "Should be invalid ($reason): $phone");
    }

    public static function invalidSpanishProvider(): array
    {
        return [
            ['000000000',         'all zeros — not a valid ES number'],
            ['1234',              'too short'],
            ['12345678901234567', 'too long (>15 digits)'],
            ['abc',               'non-numeric'],
            ['',                  'empty string'],
            ['+999000000000',     'non-existent country code'],
        ];
    }

    // -------------------------------------------------------------------------
    // validate() — international numbers
    // -------------------------------------------------------------------------

    public function testValidUKMobile(): void
    {
        $this->assertTrue(PhoneValidator::validate('+447911123456', 'GB'));
    }

    public function testValidUSNumber(): void
    {
        $this->assertTrue(PhoneValidator::validate('+12125551234', 'US'));
    }

    public function testValidFrenchMobile(): void
    {
        $this->assertTrue(PhoneValidator::validate('+33612345678', 'FR'));
    }

    // -------------------------------------------------------------------------
    // normalize() → E.164
    // -------------------------------------------------------------------------

    public function testNormalizeBareMobileToE164(): void
    {
        $this->assertSame('+34600123456', PhoneValidator::normalize('600123456', 'ES'));
    }

    public function testNormalizeWithSpacesToE164(): void
    {
        $this->assertSame('+34600123456', PhoneValidator::normalize('+34 600 123 456', 'ES'));
    }

    public function testNormalizeWithDashesToE164(): void
    {
        $this->assertSame('+34600123456', PhoneValidator::normalize('600-123-456', 'ES'));
    }

    public function testNormalizeE164InputUnchanged(): void
    {
        $this->assertSame('+34600123456', PhoneValidator::normalize('+34600123456', 'ES'));
    }

    public function testNormalizeLandlineToE164(): void
    {
        $this->assertSame('+34912345678', PhoneValidator::normalize('912345678', 'ES'));
    }

    public function testNormalizeWithIddPrefix(): void
    {
        // 0034 is the IDD prefix for Spain (equivalent to +34)
        $this->assertSame('+34600123456', PhoneValidator::normalize('0034600123456', 'ES'));
    }

    public function testNormalizeFrenchNumberWithFrContext(): void
    {
        $this->assertSame('+33612345678', PhoneValidator::normalize('0612345678', 'FR'));
    }

    public function testNormalizeUnparseableReturnsNull(): void
    {
        $this->assertNull(PhoneValidator::normalize('abc', 'ES'));
    }

    public function testNormalizeEmptyReturnsNull(): void
    {
        $this->assertNull(PhoneValidator::normalize('', 'ES'));
    }

    public function testNormalizeInvalidNumberReturnsNull(): void
    {
        $this->assertNull(PhoneValidator::normalize('000000000', 'ES'));
    }

    // -------------------------------------------------------------------------
    // format() → international display
    // -------------------------------------------------------------------------

    public function testFormatE164ToInternational(): void
    {
        // libphonenumber formats ES mobiles as: +34 6XX XX XX XX
        $this->assertSame('+34 600 12 34 56', PhoneValidator::format('+34600123456', 'ES'));
    }

    public function testFormatLandlineToInternational(): void
    {
        // libphonenumber formats ES landlines as: +34 9XX XX XX XX
        $this->assertSame('+34 912 34 56 78', PhoneValidator::format('+34912345678', 'ES'));
    }

    public function testFormatUnparseableReturnsNull(): void
    {
        $this->assertNull(PhoneValidator::format('not-a-phone', 'ES'));
    }

    public function testFormatEmptyReturnsNull(): void
    {
        $this->assertNull(PhoneValidator::format('', 'ES'));
    }

    // -------------------------------------------------------------------------
    // Country code normalization
    // -------------------------------------------------------------------------

    public function testCountryCodeCaseInsensitive(): void
    {
        // Lowercase 'es' should work same as 'ES'
        $this->assertTrue(PhoneValidator::validate('600123456', 'es'));
        $this->assertSame('+34600123456', PhoneValidator::normalize('600123456', 'es'));
    }
}
