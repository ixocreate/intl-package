<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Intl;

use Ixocreate\Contract\Application\ConfiguratorInterface;
use Ixocreate\Contract\Application\ServiceRegistryInterface;

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
     * @throws \Exception
     * @return void
     */
    public function registerService(ServiceRegistryInterface $serviceRegistry): void
    {
        $serviceRegistry->add(LocaleManager::class, new LocaleManager($this));
    }
}
