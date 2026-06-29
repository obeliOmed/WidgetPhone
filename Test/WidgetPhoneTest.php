<?php
declare(strict_types=1);

namespace FacturaScripts\Plugins\WidgetPhone\Test;

use PHPUnit\Framework\TestCase;

/**
 * Integration tests for WidgetPhone.
 *
 * Requires FacturaScripts BaseWidget. Tests auto-skip when running standalone.
 */
class WidgetPhoneTest extends TestCase
{
    private function makeWidget(array $data = []): object
    {
        if (!class_exists('FacturaScripts\Core\Lib\Widget\BaseWidget')) {
            $this->markTestSkipped('FacturaScripts BaseWidget not available in standalone test run.');
        }

        $defaults = ['fieldname' => 'phoneNumber', 'country' => 'ES'];
        $widget = new \FacturaScripts\Plugins\WidgetPhone\Lib\Widget\WidgetPhone(
            array_merge($defaults, $data)
        );
        return $widget;
    }

    private function makeRequest(array $data): object
    {
        return new class($data) {
            public object $request;
            public function __construct(array $data)
            {
                $this->request = new class($data) {
                    private array $data;
                    public function __construct(array $data) { $this->data = $data; }
                    public function get(string $key, mixed $default = null): mixed
                    {
                        return $this->data[$key] ?? $default;
                    }
                };
            }
        };
    }

    public function testProcessFormDataNormalizesE164(): void
    {
        $widget = $this->makeWidget();
        $model = new \stdClass();
        $request = $this->makeRequest(['phoneNumber' => '600123456']);
        $widget->processFormData($model, $request);
        $this->assertSame('+34600123456', $model->phoneNumber);
    }

    public function testProcessFormDataHandlesEmpty(): void
    {
        $widget = $this->makeWidget();
        $model = new \stdClass();
        $request = $this->makeRequest(['phoneNumber' => '']);
        $widget->processFormData($model, $request);
        $this->assertNull($model->phoneNumber);
    }

    public function testProcessFormDataStorasRawWhenUnparseable(): void
    {
        $widget = $this->makeWidget();
        $model = new \stdClass();
        $request = $this->makeRequest(['phoneNumber' => 'not-a-phone']);
        $widget->processFormData($model, $request);
        // Invalid input stored raw so model::test() can reject it
        $this->assertSame('not-a-phone', $model->phoneNumber);
    }

    public function testDefaultCountryIsES(): void
    {
        $widget = $this->makeWidget(['country' => 'ES']);
        $this->assertSame('ES', $widget->country);
    }

    public function testCustomCountryFR(): void
    {
        $widget = $this->makeWidget(['country' => 'FR']);
        $this->assertSame('FR', $widget->country);
    }

    public function testCountryNormalizedToUppercase(): void
    {
        $widget = $this->makeWidget(['country' => 'fr']);
        $this->assertSame('FR', $widget->country);
    }

    public function testProcessFormDataRespectsFrenchCountry(): void
    {
        $widget = $this->makeWidget(['country' => 'FR']);
        $model = new \stdClass();
        $request = $this->makeRequest(['phoneNumber' => '0612345678']);
        $widget->processFormData($model, $request);
        $this->assertSame('+33612345678', $model->phoneNumber);
    }
}
