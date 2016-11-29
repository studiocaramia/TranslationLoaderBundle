<?php

namespace Asm\TranslationLoaderBundle\DTO;

use Doctrine\ORM\Mapping as ORM;

class TranslationAdmin
{
    public $transKey;
    public $transLocale;

    protected static $translationListIndexed = [];
    protected static $domainMessages = ['General', 'Front', 'Mobile'];

    protected $translations = [];

    public function __construct($transKey, $transLocale, $messageDomain, $translation)
    {
        $this->transKey = $transKey;
        $this->transLocale = $transLocale;

        if (!isset(self::$translationListIndexed[$transLocale][$transKey])) {
            self::$translationListIndexed[$transLocale][$transKey] = $this;
        }

        $singleton = self::$translationListIndexed[$transLocale][$transKey];
        $singleton->setTranslation($messageDomain, $translation);

        return $this;
    }

    public function get($name)
    {
        if (in_array($name, self::$domainMessages)) {
            return $this->getTranslation($name);
        }
        return $this->{$name};
    }



    public function __set($name, $value)
    {
        if (in_array($name, self::$domainMessages)) {
            return $this->setTranslation($name, $value);
        }
    }

    public static function getTranslationListIndexed($locale)
    {
        return isset(self::$translationListIndexed[$locale]) ? self::$translationListIndexed[$locale] : [];
    }

    public function getTranslation($domainMessage)
    {
        return isset($this->translations[$domainMessage]) ? $this->translations[$domainMessage] : null;
    }

    public function setTranslation($domainMessage, $value)
    {
        $this->translations[$domainMessage] = $value;
    }

}