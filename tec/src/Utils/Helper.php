<?php

/**
 * 2014-2025 IT PREMIUM OU
 *
 * NOTICE OF LICENSE
 *
 * This module is licensed for use on one single domain. To use this module on additional domains,
 * you must purchase additional licenses. Redistribution, resale, leasing, licensing, or offering
 * this resource to third parties is prohibited.
 *
 * The data used in this module, especially the complete database, may not be copied.
 * It is strictly prohibited to duplicate the data and database and distribute the same,
 * and/or instruct third parties to engage in such activities, without prior consent from TecAlliance.
 * Any use of content in a manner not expressly authorized constitutes copyright infringement and violators will be prosecuted.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author IT PREMIUM OU <info@itpremium.net>
 * @copyright  2014-2025 IT PREMIUM OU.
 * @license Single Domain License
 *
 * PrestaShop and TecDoc are International Registered Trademarks, respective properties of PrestaShop SA and TecAlliance GmbH.
 * IT PREMIUM OU is not associated with TecAlliance GmbH or PrestaShop SA and their services, all rights belong to their respective owners.
 */

declare(strict_types=1);

namespace ItPremium\TecDoc\Utils;

use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Repository\LanguageRepository;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use Symfony\Component\Serializer;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Helper
{
    /**
     * Helper constructor.
     *
     * @param LanguageRepository $languageRepository
     */
    public function __construct(
        private readonly LanguageRepository $languageRepository,
    ) {
    }

    /**
     * Override the default method only for this module to prevent forms from displaying
     * Smarty errors in the debug template or preventing from using content for disabled languages.
     *
     * @param string $key
     * @param ?int $shopGroupId
     * @param ?int $shopId
     *
     * @return array
     */
    public static function getConfigInMultipleLanguages(string $key, ?int $shopGroupId = null, ?int $shopId = null): array
    {
        $resultsArray = [];

        foreach (\Language::getIDs(false) as $idLang) {
            $resultsArray[$idLang] = \Configuration::get($key, $idLang, $shopGroupId, $shopId);
        }

        return $resultsArray;
    }

    /**
     * @return \Category
     */
    public static function getTecDocCategory(): \Category
    {
        $category = new \Category(\Configuration::get(ConfigurationConstant::TECDOC_ID_CATEGORY), \Context::getContext()->language->id);

        return \Validate::isLoadedObject($category) ? $category : \Category::getRootCategory();
    }

    /**
     * @param ?string $isoCode
     *
     * @return string
     */
    public function getCurrentTecDocLanguage(?string $isoCode = null): string
    {
        $languageAvailable = $this
            ->languageRepository
            ->getLanguageByCode($isoCode ?: \Context::getContext()->language->iso_code);

        return $languageAvailable
            ? $languageAvailable->getCode()
            : \Configuration::get(key: ConfigurationConstant::TECDOC_DEFAULT_LANGUAGE_CODE, default: '');
    }

    /**
     * @param string $string
     * @param int $length
     *
     * @return string
     */
    public static function safeStringLength(string $string, int $length = 255): string
    {
        return mb_substr(trim($string), 0, $length);
    }

    /**
     * @param float $price
     *
     * @return string
     *
     * @throws LocalizationException
     */
    public static function formatPrice(float $price): string
    {
        $price = \Tools::convertPrice($price);

        return \Context::getContext()
            ->currentLocale
            ->formatPrice($price, \Context::getContext()->currency->iso_code);
    }

    /**
     * @param mixed $value
     *
     * @return float
     */
    public static function extractFloat(mixed $value): float
    {
        $value = str_replace(',', '.', $value);

        return (float) filter_var($value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
    }

    /**
     * @param mixed $date
     * @param bool $oldFormat
     *
     * @return string
     */
    public static function extractMonthFromTecDocDate(mixed $date, bool $oldFormat = true): string
    {
        if ($oldFormat) {
            $result = str_pad(
                substr((string) $date, 4, 2),
                2,
                '0',
                STR_PAD_LEFT
            );
        } else {
            $result = explode('-', $date)[1];
        }

        return $result;
    }

    /**
     * @param mixed $date
     * @param bool $oldFormat
     *
     * @return int
     */
    public static function extractYearFromTecDocDate(mixed $date, bool $oldFormat = true): int
    {
        if ($oldFormat) {
            $result = (int) substr((string) $date, 0, 4);
        } else {
            $result = explode('-', $date)[0];
        }

        return $result;
    }

    /**
     * @param int $startYear
     *
     * @return array
     */
    public static function getYears(int $startYear = 1965): array
    {
        return array_map(function ($year) {
            return [
                'value' => $year,
            ];
        }, range($startYear, date('Y')));
    }

    /**
     * @param string $fileName
     *
     * @return string
     */
    public static function getFileExtension(string $fileName): string
    {
        return strtolower(
            pathinfo($fileName, PATHINFO_EXTENSION)
        );
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function prepareName(string $string): string
    {
        $string = str_replace('/', ' / ', $string);
        $string = preg_replace('/,(?!\s)/', ', ', $string);

        return mb_strtoupper(mb_substr($string, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr(mb_convert_case($string, MB_CASE_LOWER, 'UTF-8'), 1, mb_strlen($string), 'UTF-8');
    }

    /**
     * @param $keyword
     *
     * @return string
     */
    public static function prepareKeyword($keyword): string
    {
        $keyword = trim($keyword);

        $characters = [
            '-', ' ',
        ];

        return str_replace($characters, '', $keyword);
    }

    /**
     * @param array $array
     *
     * @return array
     */
    public static function prepareArrayForSelect(array $array): array
    {
        $newArr = [];

        foreach ($array as $id => $name) {
            $newArr[] = [
                'id' => $id,
                'name' => $name,
            ];
        }

        return $newArr;
    }

    /**
     * @param string $url
     *
     * @return bool
     */
    public static function validateUrl(string $url): bool
    {
        return (bool) preg_match('%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu', $url);
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    public static function validateFile(string $filePath): bool
    {
        return $filePath and is_file($filePath) and is_readable($filePath) and filesize($filePath);
    }

    /**
     * @param $value
     *
     * @return array
     */
    public static function createMultiLangValue($value): array
    {
        $multiLangArr = [];

        foreach (\Language::getLanguages(false) as $language) {
            $multiLangArr[$language['id_lang']] = $value;
        }

        return $multiLangArr;
    }

    /**
     * @param int $int
     *
     * @return int
     */
    public static function preventNegativeInt(int $int): int
    {
        return max($int, 0);
    }

    /**
     * @param string $reference
     *
     * @return string
     */
    public static function prepareReference(string $reference): string
    {
        // $reference = mb_strtolower($reference);
        // return str_replace(' ', '', $reference);

        return mb_strtolower($reference);
    }

    /**
     * @param string $string
     *
     * @return string
     */
    public static function urlEncode(string $string): string
    {
        return str_replace('%2F', '/', urlencode($string));
    }

    /**
     * @param mixed $object
     *
     * @return string
     */
    public static function serializeObjectToJson(mixed $object): string
    {
        $serializer = new Serializer\Serializer(
            normalizers: [
                new Serializer\Normalizer\ObjectNormalizer(),
            ],
            encoders: ['json' => new Serializer\Encoder\JsonEncoder()]
        );

        return $serializer->serialize($object, 'json');
    }
}
