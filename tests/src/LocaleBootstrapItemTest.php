<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Test\Intl;

use Ixocreate\Application\Configurator\ConfiguratorInterface;
use Ixocreate\Intl\LocaleBootstrapItem;
use PHPUnit\Framework\TestCase;

class LocaleBootstrapItemTest extends TestCase
{
    public function setUp()
    {
        $this->local = new LocaleBootstrapItem();
    }

    /**
     * @covers \Ixocreate\Intl\LocaleBootstrapItem
     */
    public function testLocaleBootstrapItem()
    {
        $this->assertSame($this->local->getVariableName(), 'locale');
        $this->assertSame($this->local->getFileName(), 'locale.php');
        $this->assertInstanceOf(ConfiguratorInterface::class, $this->local->getConfigurator());
    }
}
