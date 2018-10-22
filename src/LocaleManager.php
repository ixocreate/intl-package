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

use KiwiSuite\Contract\Application\SerializableServiceInterface;

final class LocaleManager implements SerializableServiceInterface
{
    /**
     * @var array
     */
    private $locales = [];

    /**
     * @var string
     */
    private $default;

    /**
     * LocaleManager constructor.
     * @param LocaleConfigurator $localeConfigurator
     */
    public function __construct(LocaleConfigurator $localeConfigurator)
    {
        $this->locales = $localeConfigurator->getLocales();

        $this->default = $localeConfigurator->getDefault();
        if (empty($this->default) && !empty($this->locales)) {
            $this->default = \array_keys($this->locales)[0];
        }

        if (empty($this->default)) {
            $this->default = "";
        }

    }

    /**
     * @return array
     */
    public function all(): array
    {
        return \array_values($this->locales);
    }

    /**
     * @return array
     */
    public function allActive(): array
    {
        return \array_filter($this->all(), function ($options) {
            return $options['active'];
        });
    }

    /**
     * @param string $locale
     * @return bool
     */
    public function has(string $locale): bool
    {
        return array_key_exists($locale, $this->locales);
    }

    /**
     * @return string
     */
    public function defaultLocale(): string
    {
        return $this->default;
    }

    /**
     * @param string $locale
     */
    public function acceptLocale(string $locale): void
    {
        //TODO check locale
        \Locale::setDefault($locale);
        \setlocale(LC_ALL, $locale, $locale . ".utf8", $locale . ".UTF-8");
    }

    /**
     * @param string $locale
     * @param callable $callback
     */
    public function callWithDifferentLocale(string $locale, callable $callback): void
    {
        $defaultLocale = \Locale::getDefault();
        $this->acceptLocale($locale);

        $callback();

        $this->acceptLocale($defaultLocale);
    }

    /**
     * @param string|null $header
     * @return string
     */
    public function suggestLocale(string $header = null): string
    {
        $locale = null;

        if (!empty($header)) {
            $locale = \Locale::acceptFromHttp($header);
            if (!$this->has($locale)) {
                $locale = null;
            }
        }

        if (empty($locale)) {
            $locale = $this->defaultLocale();
        }

        return $locale;
    }

    /**
     * @return string
     */
    public function serialize()
    {
        return \serialize([
            'locales' => $this->locales,
            'default' => $this->default,
        ]);
    }

    /**
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $unserialize = \unserialize($serialized);
        $this->locales = $unserialize['locales'];
        $this->default = $unserialize['default'];
    }
}
