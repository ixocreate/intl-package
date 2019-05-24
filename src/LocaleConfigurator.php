<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Intl;

use Ixocreate\Application\Configurator\ConfiguratorInterface;
use Ixocreate\Application\Service\ServiceRegistryInterface;

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
        if (\Locale::canonicalize($locale) !== $locale) {
            throw new \InvalidArgumentException("Local $locale is not a valid local, use: " . \Locale::canonicalize($locale));
        }
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
        if (!\array_key_exists($locale, $this->getLocales())) {
            throw new \InvalidArgumentException("locale $locale is not set");
        }

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
