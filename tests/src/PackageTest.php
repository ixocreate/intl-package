<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\Intl;

use Ixocreate\Application\Configurator\ConfiguratorRegistryInterface;
use Ixocreate\Application\Service\ServiceRegistryInterface;
use Ixocreate\Intl\LocaleBootstrapItem;
use Ixocreate\Intl\LocaleConfigurator;
use Ixocreate\Intl\LocaleManager;
use Ixocreate\Intl\Package;
use Ixocreate\ServiceManager\ServiceManagerInterface;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{
    /**
     * @covers \Ixocreate\Intl\Package
     */
    public function testPackage()
    {
        $package = new Package();

        $this->assertSame([LocaleBootstrapItem::class], $package->getBootstrapItems());
        $this->assertNull($package->getBootstrapDirectory());
        $this->assertEmpty($package->getDependencies());
    }

    /**
     * @covers \Ixocreate\Intl\Package
     */
    public function testBoot()
    {
        $localeConfigurator = new LocaleConfigurator();
        $localeConfigurator->add('fi_FI', 'Finnish');
        $localeConfigurator->setDefaultLocale('fi_FI');

        $localeManager = new LocaleManager($localeConfigurator);

        $serviceManager = $this->getMockBuilder(ServiceManagerInterface::class)->getMock();
        $serviceManager
            ->expects($this->once())
            ->method('get')
            ->with($this->equalTo(LocaleManager::class))
            ->willReturn($localeManager);

        $package = new Package();
        $package->boot($serviceManager);

        $this->assertEquals(\Locale::getDefault(), $localeConfigurator->getDefault());
    }
}
