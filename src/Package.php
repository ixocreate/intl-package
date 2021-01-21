<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Intl;

use Ixocreate\Application\Package\BootInterface;
use Ixocreate\Application\Package\PackageInterface;
use Ixocreate\ServiceManager\ServiceManagerInterface;

final class Package implements PackageInterface, BootInterface
{
    /**
     * @return array
     */
    public function getBootstrapItems(): array
    {
        return [
            LocaleBootstrapItem::class,
        ];
    }

    /**
     * @param ServiceManagerInterface $serviceManager
     */
    public function boot(ServiceManagerInterface $serviceManager): void
    {
        /** @var LocaleManager $localeManager */
        $localeManager = $serviceManager->get(LocaleManager::class);
        $localeManager->acceptLocale($localeManager->defaultLocale());
    }

    /**
     * @return null|string
     */
    public function getBootstrapDirectory(): ?string
    {
        return null;
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return [];
    }
}
