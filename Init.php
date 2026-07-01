<?php
declare(strict_types=1);

namespace FacturaScripts\Plugins\WidgetPhone;

use FacturaScripts\Core\Template\InitClass;

/**
 * Plugin lifecycle hooks. Widget auto-registration is handled by FacturaScripts PluginsDeploy
 * — no manual registration needed here.
 */
class Init extends InitClass
{
    public function init(): void
    {
        // FacturaScripts has a single shared root vendor/ and never merges a plugin's
        // composer.json dependencies into it. giggsey/libphonenumber-for-php is required
        // by this plugin, so it ships its own vendor/ (committed to the repo) and is
        // loaded here on every request — no manual `composer require` at the FS root needed.
        if (!class_exists('libphonenumber\\PhoneNumberUtil')) {
            $autoload = __DIR__ . '/vendor/autoload.php';
            if (is_readable($autoload)) {
                require_once $autoload;
            }
        }
    }

    public function update(): void
    {
    }

    public function uninstall(): void
    {
    }
}
