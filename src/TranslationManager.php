<?php

namespace Meisterwerk\Core;

use Symfony\Component\Yaml\Yaml;

class TranslationManager
{
    private const DEFAULT_LANGUAGE = 'de';

    private string $projectPath;

    private string $defaultFilePath;

    private array $translations;

    public function __construct(string $projectPath)
    {
        $this->projectPath = $projectPath;
        $this->defaultFilePath = "{$projectPath}/locales/".self::DEFAULT_LANGUAGE.".yml";
        $this->translations = [];
    }

    public function getText(string $keyString, string $lang = self::DEFAULT_LANGUAGE, array $templateVars = []): string
    {
        $languageData = $this->getLanguageData($lang);
        $keyArray = explode('.', $keyString);
        return $this->getTextRec(
            $languageData,
            $keyArray,
            $templateVars
        );
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

    private function getLanguageData(string $lang): array
    {
        if(!array_key_exists($lang, $this->translations)) {
            $this->loadTranslations($lang);
        }
        return array_key_exists($lang, $this->translations)
            ? $this->translations[$lang]
            : $this->translations[self::DEFAULT_LANGUAGE];
    }

    private function loadTranslations(string $lang): void
    {
        $filePath = "{$this->projectPath}/locales/{$lang}.yml";
        if(file_exists($filePath)) {
            $loadedFile = Yaml::parseFile($filePath);
            $this->translations[$lang] = $loadedFile;
        } elseif (
            !array_key_exists(self::DEFAULT_LANGUAGE, $this->translations)
            && file_exists($this->defaultFilePath)
        ) {
            $defaultFile = Yaml::parseFile($this->defaultFilePath);
            $this->translations[self::DEFAULT_LANGUAGE] = $defaultFile;
        } else {
            throw new \RuntimeException("default language file doesn't exist");
        }
    }
}