<?php

namespace Meisterwerk\Core;

use Symfony\Component\Yaml\Yaml;

class TranslationManager
{
    private array $translations;

    public function __construct(string $projectPath, array $languageKeys, string $defaultLang = 'de')
    {
        $translations = [];
        foreach ($languageKeys as $lang) {
            $filePath = "{$projectPath}/locales/{$lang}.yml";
            if(file_exists($filePath)) {
                $loadedFile = Yaml::parseFile($filePath);
                $translations[$lang] = $loadedFile;
            }
        }
        if(array_key_exists($defaultLang, $translations)) {
            $translations['default'] = $translations[$defaultLang];
        }
        $this->translations = $translations;
    }

    public function getText(string $lang, string $keyString, array $templateVars = []): string
    {
        $keyArray = explode('.', $keyString);
        if(array_key_exists($lang, $this->translations)) {
            return $this->getTextRec(
                $this->translations[$lang],
                $keyArray,
                $templateVars
            );
        } elseif (array_key_exists('default', $this->translations)) {
            return $this->getTextRec(
                $this->translations['default'],
                $keyArray,
                $templateVars
            );
        }
        return ''; //todo: handling?
    }

    private function getTextRec(array $languageData, array $keyArray, array $templateVars = []): string
    {
        $key = $keyArray[0];
        $text = $languageData[$key];
        array_shift($keyArray);
        if (empty($keyArray)) {
            return sprintf($text, ...$templateVars);
        }
        return self::getTextRec($text, $keyArray, $templateVars);
    }
}