<?php
/**
 * @link https://github.com/ixocreate
 * @copyright IXOCREATE GmbH
 * @license MIT License
 */

declare(strict_types=1);

namespace IxocreateTest\Intl;

use Ixocreate\Intl\LocaleConfigurator;
use Ixocreate\Intl\LocaleManager;
use PHPUnit\Framework\TestCase;

class LocaleManagerTest extends TestCase
{
    /**
     * @covers \Ixocreate\Intl\LocaleManager::__construct
     */
    public function testLocaleManager()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->setDefaultLocale('Hallo');
        $localManager = new LocaleManager($localeConfig);

        $this->assertIsArray($localManager->all());
        $this->assertIsArray($localManager->allActive());
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager
     */
    public function testLocaleManagerNoDefault()
    {
        $this->expectException(\RuntimeException::class);

        $localeConfig = new LocaleConfigurator();
        $this->localManager = new LocaleManager($localeConfig);
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager
     */
    public function testLocalManagerEmptyDefault()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('Test');
        $localManager = new LocaleManager($localeConfig);

        $this->assertIsString($localManager->defaultLocale());
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::allActive
     */
    public function testAllActive()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('Test');
        $localManager = new LocaleManager($localeConfig);

        $this->assertIsArray($localManager->allActive());
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::serialize
     */
    public function testSerialize()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('Test');
        $localeConfig->setDefaultLocale('Hallo');
        $localeManager = new LocaleManager($localeConfig);

        $this->assertSame(\serialize([
            'locales' => [ 'Test' => [ 'locale' => 'Test', 'active' => true, 'name' => 'test']],
            'default' => 'Hallo',
        ]), $localeManager->serialize());
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::unserialize
     */
    public function testUnserialize()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('Test');
        $localeConfig->setDefaultLocale('Hallo');
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
        $localeManager = new LocaleManager($localeConfig);


        $localeManager->suggestLocale('de-DE,fr');
        $this->assertSame($localeManager->defaultLocale(), $localeManager->suggestLocale('de-DE,fr'));
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::suggestLocale
     */
    public function testSuggestLocaleLocaleHasNot()
    {
        $localeConfig = new LocaleConfigurator();
        $localeConfig->add('de_AT');
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
        $localeConfig->setDefaultLocale('de_DE');
        $localeManager = new LocaleManager($localeConfig);

        $localeManager->suggestLocale('de-DE,fr');

        $this->assertSame($localeManager->defaultLocale(), $localeManager->suggestLocale('de-DE,fr'));
    }

    /**
     * @covers \Ixocreate\Intl\LocaleManager::callWithDifferentLocale
     */
    public function testCallWithDifferentLocale()
    {
        $localeConfig = new LocaleConfigurator();
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
        $localeConfig->setDefaultLocale('de_DE');
        $localeManager = new LocaleManager($localeConfig);

        $localeManager->acceptLocale('en_US');
        $this->assertSame('en_US', \Locale::getDefault());
    }
}
