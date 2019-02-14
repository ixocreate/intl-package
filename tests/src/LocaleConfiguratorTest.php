<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateTest\Intl;

use Ixocreate\Contract\Application\ServiceRegistryInterface;
use Ixocreate\Intl\LocaleConfigurator;
use Ixocreate\Intl\LocaleManager;
use PHPUnit\Framework\TestCase;

class LocaleConfiguratorTest extends TestCase
{
    /**
     * @covers \Ixocreate\Intl\LocaleConfigurator
     */
    public function testLocalConfigurator()
    {
        $collector = [];
        $serviceRegistry = $this->createMock(ServiceRegistryInterface::class);
        $serviceRegistry->method('add')->willReturnCallback(function ($name, $object) use (&$collector) {
            $collector[$name] = $object;
        });


        $configurator = new LocaleConfigurator();

        $configurator->setDefaultLocale('Test');
        $this->assertSame('Test', $configurator->getDefault());

        $configurator->add('test', false);
        $configurator->add('test', true, 'anders');

        $configurator->registerService($serviceRegistry);


        $this->assertArrayHasKey(LocaleManager::class, $collector);
    }
}
