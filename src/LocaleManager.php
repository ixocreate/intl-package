<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Intl;

use Ixocreate\Contract\Application\SerializableServiceInterface;

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
        if (empty($this->locales)) {
            throw new \RuntimeException('locales is empty');
        }

        $this->default = $localeConfigurator->getDefault();
        if (empty($this->default)) {
            throw new \RuntimeException('default is empty');
        }

        $this->acceptLocale($this->default);
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
        return \array_key_exists($locale, $this->locales);
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
        if (\Locale::canonicalize($locale) !== $locale) {
            throw new \InvalidArgumentException("Local $locale is not a valid local, use: " . \Locale::canonicalize($locale));
        }

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
            $requestedLocale = \Locale::acceptFromHttp($header);
            if ($this->has($requestedLocale)) {
                $locale = $requestedLocale;
            } else {
                $language = \Locale::getPrimaryLanguage($requestedLocale);
                foreach ($this->locales as $tmpLocale) {
                    if ($language == \Locale::getPrimaryLanguage($tmpLocale['locale'])) {
                        $locale = $tmpLocale['locale'];
                        break;
                    }
                }
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
