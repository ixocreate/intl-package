<?php
/**
 * kiwi-suite/intl (https://github.com/kiwi-suite/intl)
 *
 * @package kiwi-suite/intl
 * @see https://github.com/kiwi-suite/intl
 * @copyright Copyright (c) 2010 - 2018 kiwi suite GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace KiwiSuite\Intl\BootstrapItem;

use KiwiSuite\Contract\Application\BootstrapItemInterface;
use KiwiSuite\Contract\Application\ConfiguratorInterface;
use KiwiSuite\Intl\LocaleConfigurator;

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
