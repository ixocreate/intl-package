<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Intl\Package\BootstrapItem;

use Ixocreate\Application\Bootstrap\BootstrapItemInterface;
use Ixocreate\Application\ConfiguratorInterface;
use Ixocreate\Intl\Package\LocaleConfigurator;

final class LocaleBootstrapItem implements BootstrapItemInterface
{
    /**
     * @return mixed
     */
    public function getConfigurator(): ConfiguratorInterface
    {
        return new LocaleConfigurator();
    }

    /**
     * @return string
     */
    public function getVariableName(): string
    {
        return "locale";
    }

    /**
     * @return string
     */
    public function getFileName(): string
    {
        return "locale.php";
    }
}
