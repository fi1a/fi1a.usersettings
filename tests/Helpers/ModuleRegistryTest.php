<?php

declare(strict_types=1);

namespace Fi1a\Unit\UserSettings\Helpers;

use Fi1a\Unit\UserSettings\TestCase\ModuleTestCase;
use Fi1a\UserSettings\Helpers\ModuleRegistry;
use ReflectionClass;

/**
 * Тестирование реестра
 */
class ModuleRegistryTest extends ModuleTestCase
{
    /**
     * Тестирование методов
     */
    public function testConfigure(): void
    {
        $reflection = new ReflectionClass(ModuleRegistry::class);
        $globals = $reflection->getProperty('globals');
        $globals->setAccessible(true);
        $application = $reflection->getProperty('application');
        $application->setAccessible(true);
        $applicationValue = $application->getValue();
        $globalValue = $globals->getValue();
        ModuleRegistry::configure($applicationValue, $globalValue);
        $this->assertInstanceOf(get_class($applicationValue), ModuleRegistry::getApplication());
        ModuleRegistry::setGlobals('FUS_TEST_GLOBAL', 'value');
        $this->assertEquals('value', ModuleRegistry::getGlobals('FUS_TEST_GLOBAL'));
    }
}
