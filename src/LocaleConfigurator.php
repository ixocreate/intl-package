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
namespace KiwiSuite\Intl;

use KiwiSuite\Contract\Application\ConfiguratorInterface;
use KiwiSuite\Contract\Application\ServiceRegistryInterface;

final class LocaleConfigurator implements ConfiguratorInterface
{
    /**
     * @var array
     */
    private $locales = [];

    /**
     * @var
     */
    private $default;

    /**
     * @param string $locale
     * @param bool $active
     * @param null $name
     */
    public function add(string $locale, $active = true, $name = null): void
    {
        $this->locales[$locale] = [
            'locale' => $locale,
            'active' => $active,
            'name' => (empty($name)) ? \Locale::getDisplayLanguage($locale) : $name,
        ];
    }

    /**
     * @return array
     */
    public function getLocales(): array
    {
        return $this->locales;
    }

    /**
     * @param string $locale
     */
    public function setDefaultLocale(string $locale): void
    {
        //TODO check if $locale is set
        $this->default = $locale;
    }

    /**
     * @return null|string
     */
    public function getDefault(): ?string
    {
        return $this->default;
    }

    /**
     * @param ServiceRegistryInterface $serviceRegistry
     * @return void
     * @throws \Exception
     */
    public function registerService(ServiceRegistryInterface $serviceRegistry): void
    {
        $serviceRegistry->add(LocaleManager::class, new LocaleManager($this));
    }
}
