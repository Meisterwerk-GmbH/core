<?php

namespace Meisterwerk\Core;

use Symfony\Component\Yaml\Yaml;

class TranslationManager
{
    private array $translations;

    public function __construct(string $projectPath, array $languageKeys)
    {
        $translations = [];
        foreach ($languageKeys as $lang) {
            $loadedFile = Yaml::parseFile("{$projectPath}/{$lang}.yml");
            $translations[$lang] = $loadedFile;
        }
        $this->translations = $translations;
    }

    public function getText(string $lang, string $keyString, array $templateVars = []): string
    {
        $keyArray = explode('.', $keyString);
        $languageData = $this->translations[$lang];
        return $this->getTextRec($languageData, $keyArray, $templateVars);
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