<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOLIT GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace Ixocreate\Intl;

use Ixocreate\Application\Service\SerializableServiceInterface;

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
     *
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
        $requestedLocaleArray = $this->getAcceptLanguageList($header);
        $secondTryLocales = [];
        foreach ($requestedLocaleArray as $value) {
            $region = \Locale::getRegion($value);
            if (!empty($region)) {
                $secondTryLocales[] = $value;
                if ($this->has($value)) {
                    $locale = $value;
                    break;
                }
            } else {
                $language = \Locale::getPrimaryLanguage($value);
                foreach ($this->locales as $tmpLocale) {
                    if ($language == \Locale::getPrimaryLanguage($tmpLocale['locale'])) {
                        $locale = $tmpLocale['locale'];
                        break 2;
                    }
                }
            }
        }
        if (empty($locale)) {
            foreach ($secondTryLocales as $value) {
                $language = \Locale::getPrimaryLanguage($value);
                foreach ($this->locales as $tmpLocale) {
                    if ($language == \Locale::getPrimaryLanguage($tmpLocale['locale'])) {
                        $locale = $tmpLocale['locale'];
                        break 2;
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

    private function getAcceptLanguageList($header)
    {
        if (!empty($header)) {
            \preg_match_all('/([a-z]{1,8}(?!=)(-[a-z]{1,8})?)\s*(;\s*q\s*=\s*(1|0\.[0-9]+))?/i', $header, $lang_parse);

            if (\count($lang_parse[1])) {
                $langs = [];
                for ($i = 0; $i < \count($lang_parse[1]); $i++) {
                    $lang = \Locale::canonicalize($lang_parse[1][$i]);
                    $langs[$lang] = ($lang_parse[4][$i] === '') ? 1 : (float)$lang_parse[4][$i];
                }
                \arsort($langs, SORT_NUMERIC);
                return \array_keys($langs);
            }
        }
        return [];
    }
}
