<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\Intl;

use InvalidArgumentException;
use Ixocreate\Application\Service\ServiceRegistryInterface;
use Ixocreate\Intl\LocaleConfigurator;
use Ixocreate\Intl\LocaleManager;
use PHPUnit\Framework\TestCase;

class LocaleConfiguratorTest extends TestCase
{
    /**
     * @covers \Ixocreate\Intl\LocaleConfigurator::add
     */
    public function testLocalConfiguratorAdd()
    {
        $collector = [];
        $serviceRegistry = $this->createMock(ServiceRegistryInterface::class);
        $serviceRegistry->method('add')->willReturnCallback(function ($name, $object) use (&$collector) {
            $collector[$name] = $object;
        });

        $configurator = new LocaleConfigurator();

        $configurator->add('en_US', false);
        $configurator->add('de_DE', true, 'anders');


        $this->expectException(InvalidArgumentException::class);
        $configurator->add('en_Us');
    }

    /**
     * @covers \Ixocreate\Intl\LocaleConfigurator::setDefaultLocale
     */
    public function testSetDefault()
    {
        $collector = [];
        $serviceRegistry = $this->createMock(ServiceRegistryInterface::class);
        $serviceRegistry->method('add')->willReturnCallback(function ($name, $object) use (&$collector) {
            $collector[$name] = $object;
        });

        $configurator = new LocaleConfigurator();
        $configurator->add('de_DE');

        $configurator->setDefaultLocale('de_DE');
        $this->assertSame('de_DE', $configurator->getDefault());

        $this->expectException(InvalidArgumentException::class);
        $configurator->setDefaultLocale('en_Us');
    }

    /**
     * @covers \Ixocreate\Intl\LocaleConfigurator
     */
    public function testLocaleConfigurator()
    {
        $collector = [];
        $serviceRegistry = $this->createMock(ServiceRegistryInterface::class);
        $serviceRegistry->method('add')->willReturnCallback(function ($name, $object) use (&$collector) {
            $collector[$name] = $object;
        });

        $configurator = new LocaleConfigurator();
        $configurator->add('de_DE');

        $configurator->setDefaultLocale('de_DE');

        $this->assertArrayHasKey('de_DE', $configurator->getlocales());
        $this->assertSame('de_DE', $configurator->getDefault());
        $configurator->registerService($serviceRegistry);
        $this->assertArrayHasKey(LocaleManager::class, $collector);
    }
}
