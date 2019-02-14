<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateTest\Intl\BootstrapItem;

use Ixocreate\Contract\Application\ConfiguratorInterface;
use Ixocreate\Intl\BootstrapItem\LocaleBootstrapItem;
use PHPUnit\Framework\TestCase;

class LocaleBootstrapItemTest extends TestCase
{
    public function setUp()
    {
        $this->local = new LocaleBootstrapItem();
    }

    /**
     * @covers \Ixocreate\Intl\BootstrapItem\LocaleBootstrapItem
     */
    public function testPackage()
    {
        $this->assertSame($this->local->getVariableName(), 'locale');
        $this->assertSame($this->local->getFileName(), 'locale.php');
        $this->assertInstanceOf(ConfiguratorInterface::class, $this->local->getConfigurator());
    }
}
