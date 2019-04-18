<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\Intl;

use Ixocreate\Application\ConfiguratorRegistryInterface;
use Ixocreate\Application\ServiceRegistryInterface;
use Ixocreate\ServiceManager\ServiceManagerInterface;
use Ixocreate\Intl\Package\Bootstrap\LocaleBootstrapItem;
use Ixocreate\Intl\Package\Package;
use PHPUnit\Framework\TestCase;

class PackageTest extends TestCase
{
    /**
     * @var Package
     */
    private $package;

    public function setUp()
    {
        $this->package = new Package();
    }

    /**
     * @covers \Ixocreate\Intl\Package\Package
     */
    public function testPackage()
    {
        $ConfiguratorRegistry = $this->getMockBuilder(ConfiguratorRegistryInterface::class)->getMock();
        $ServiceRegistry = $this->getMockBuilder(ServiceRegistryInterface::class)->getMock();
        $ServiceManager = $this->getMockBuilder(ServiceManagerInterface::class)->getMock();

        $test = new Package();

        $test->configure($ConfiguratorRegistry);
        $test->addServices($ServiceRegistry);
        $test->boot($ServiceManager);

        $this->assertSame([LocaleBootstrapItem::class], $this->package->getBootstrapItems());
        $this->assertNull($this->package->getConfigProvider());
        $this->assertNull($this->package->getBootstrapDirectory());
        $this->assertNull($this->package->getConfigDirectory());
        $this->assertNull($this->package->getDependencies());
    }
}
