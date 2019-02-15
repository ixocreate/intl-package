<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateTest\Intl;

use InvalidArgumentException;
use Ixocreate\Intl\LocaleConfigurator;
use Ixocreate\Intl\LocaleManager;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class LocaleManagerTest extends TestCase
{
    /**
     * @covers \Ixocreate\Intl\LocaleManager::__construct
     */
    public function testLocaleManager()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_DE');
        $localeConfig->setDefaultLocale('de_DE');
        $localManager = new LocaleManager($localeConfig);

        $this->assertIsArray($localManager->all());
        $this->assertIsArray($localManager->allActive());
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::__construct
     */
    public function testLocalManagerEmptyLocales()
    {
        $this->expectException(RuntimeException::class);
        $localeConfig = new LocaleConfigurator();
        $localManager = new LocaleManager($localeConfig);
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::__construct
     */
    public function testLocalManagerEmptyDefault()
    {
        $this->expectException(RuntimeException::class);

        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_DE');
        $localManager = new LocaleManager($localeConfig);
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::allActive
     */
    public function testAllActive()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_DE');
        $localeConfig->setDefaultLocale('de_DE');
        $localManager = new LocaleManager($localeConfig);

        $this->assertIsArray($localManager->allActive());
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::serialize
     */
    public function testSerialize()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_DE');
        $localeConfig->setDefaultLocale('de_DE');
        $localeManager = new LocaleManager($localeConfig);

        $this->assertSame(\serialize([
            'locales' => [ 'de_DE' => [ 'locale' => 'de_DE', 'active' => true, 'name' => 'Deutsch']],
            'default' => 'de_DE',
        ]), $localeManager->serialize());
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::unserialize
     */
    public function testUnserialize()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_DE');
        $localeConfig->setDefaultLocale('de_DE');
        $localeManager = new LocaleManager($localeConfig);

        $s = \serialize($localeManager);

        $temp = \unserialize($s);

        $this->assertSame($localeManager->all(), $temp->all());
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::suggestLocale
     */
    public function testSuggestLocaleLocaleHas()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_DE');
        $localeConfig->add('en_US');
        $localeConfig->add('fr_CH');
        $localeConfig->setDefaultLocale('de_DE');
        $localeManager = new LocaleManager($localeConfig);

        $this->assertSame('en_US', $localeManager->suggestLocale('fr-FR, fr;q=0.8, en;q=0.9, de;q=0.7, *;q=0.5'));

        $this->assertSame('fr_CH', $localeManager->suggestLocale('fr-FR, fr-BE, en-GB, *;q=0.5'));

        $this->assertSame('fr_CH', $localeManager->suggestLocale('fr-FR, fr;q=0.8, en-GB;q=0.9, de;q=0.7, *;q=0.5'));
        $this->assertSame('de_DE', $localeManager->suggestLocale('fr-FR, fr-BE;q=0.8, en-GB;q=0.9, de;q=0.7, *;q=0.5'));
        $this->assertSame('fr_CH', $localeManager->suggestLocale('fr-FR, fr-BE;q=0.8, en-GB;q=0.9, de-AT;q=0.7, *;q=0.5'));


        $this->assertSame('fr_CH', $localeManager->suggestLocale('fr-FR, fr, en;q=0.9, de;q=0.7, *;q=0.5'));
        $this->assertSame('de_DE', $localeManager->suggestLocale('de-DE,fr'));
        $this->assertSame('de_DE', $localeManager->suggestLocale('de,fr'));
        $this->assertSame('en_US', $localeManager->suggestLocale('en-US,fr'));
        $this->assertSame('fr_CH', $localeManager->suggestLocale('es-ES,fr'));
        $this->assertSame('de_DE', $localeManager->suggestLocale('es-ES,de-DE;q=0.5'));
        $this->assertSame('de_DE', $localeManager->suggestLocale('en-US;q=0.7,de-DE'));
        $this->assertSame('de_DE', $localeManager->suggestLocale('es-ES, nl-BE'));

        $this->assertSame('fr_CH', $localeManager->suggestLocale('us-UK,fr'));


        // test invalid locale

        $this->assertSame($localeManager->defaultLocale(), $localeManager->suggestLocale('de-DE,fr'));
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::suggestLocale
     */
    public function testSuggestLocaleLocaleHasNot()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_AT');
        $localeConfig->setDefaultLocale('de_AT');
        $localeManager = new LocaleManager($localeConfig);

        $localeManager->suggestLocale('de-DE,fr');

        $this->assertSame($localeManager->defaultLocale(), $localeManager->suggestLocale('de-DE,fr'));
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::suggestLocale
     */
    public function testSuggestLocaleLocaleempty()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_DE');
        $localeConfig->setDefaultLocale('de_DE');
        $localeManager = new LocaleManager($localeConfig);

        $locale = $localeManager->suggestLocale('en_US,fr');

        $this->assertSame($localeManager->defaultLocale(), $locale);
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::callWithDifferentLocale
     */
    public function testCallWithDifferentLocale()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_DE');
        $localeConfig->setDefaultLocale('de_DE');
        $localeManager = new LocaleManager($localeConfig);

        $checkLocale = null;
        $test = function () use (&$checkLocale) {
            $checkLocale = \Locale::getDefault();
        };

        $this->assertSame('de_DE', \Locale::getDefault());
        $localeManager->callWithDifferentLocale('en_US', $test);
        $this->assertSame('de_DE', \Locale::getDefault());
        $this->assertSame('en_US', $checkLocale);
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::acceptLocale
     */
    public function testAcceptLocale()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_DE');
        $localeConfig->setDefaultLocale('de_DE');
        $localeManager = new LocaleManager($localeConfig);

        $localeManager->acceptLocale('en_US');
        $this->assertSame('en_US', \Locale::getDefault());
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::acceptLocale
     */
    public function testAcceptLocaleNotValid()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_DE');
        $localeConfig->setDefaultLocale('de_DE');
        $localeManager = new LocaleManager($localeConfig);

        $this->expectException(InvalidArgumentException::class);
        $localeManager->acceptLocale('en_US...');
        $this->assertSame('en_US', \Locale::getDefault());
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::all
     */
    public function testLocaleManagerAll()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_DE');
        $localeConfig->setDefaultLocale('de_DE');
        $localManager = new LocaleManager($localeConfig);

        $this->assertIsArray($localManager->all());
        $this->assertIsArray($localManager->allActive());
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::has
     */
    public function testLocaleManagerHas()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_DE');
        $localeConfig->setDefaultLocale('de_DE');
        $localManager = new LocaleManager($localeConfig);


        $this->assertIsBool($localManager->has('de_DE'));
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::defaultLocale
     */
    public function testLocaleManagerDefault()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_DE');
        $localeConfig->setDefaultLocale('de_DE');
        $localManager = new LocaleManager($localeConfig);


        $this->assertSame('de_DE', $localManager->defaultLocale());
    }
}
