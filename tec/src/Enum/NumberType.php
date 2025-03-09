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

namespace ItPremium\TecDoc\Enum;

use ItPremium\TecDoc\Enum\Trait\EnumToArray;
use ItPremium\TecDoc\Enum\Trait\HasTranslations;

if (!defined('_PS_VERSION_')) {
    exit;
}

enum NumberType: int
{
    use EnumToArray;
    use HasTranslations;

    case ARTICLE_NUMBER = 0;
    case OE_NUMBER = 1;
    case TRADE_NUMBER = 2;
    case COMPARABLE_NUMBER = 3;
    case REPLACEMENT_NUMBER = 4;
    case REPLACED_NUMBER = 5;
    case EAN_NUMBER = 6;
    case ANY_NUMBER = 10;
    // case FREE_TEXT = 99;

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::ARTICLE_NUMBER => self::getTranslator()->trans('Article number', [], 'Modules.Itptecdoc.Enums'),
            self::OE_NUMBER => self::getTranslator()->trans('OE number', [], 'Modules.Itptecdoc.Enums'),
            self::TRADE_NUMBER => self::getTranslator()->trans('Trade number', [], 'Modules.Itptecdoc.Enums'),
            self::COMPARABLE_NUMBER => self::getTranslator()->trans('Comparable number', [], 'Modules.Itptecdoc.Enums'),
            self::REPLACEMENT_NUMBER => self::getTranslator()->trans('Replacement number', [], 'Modules.Itptecdoc.Enums'),
            self::REPLACED_NUMBER => self::getTranslator()->trans('Replaced number', [], 'Modules.Itptecdoc.Enums'),
            self::EAN_NUMBER => self::getTranslator()->trans('EAN number', [], 'Modules.Itptecdoc.Enums'),
            self::ANY_NUMBER => self::getTranslator()->trans('Any number', [], 'Modules.Itptecdoc.Enums'),
            // self::FREE_TEXT => self::getTranslator()->trans('Free text', [], 'Modules.Itptecdoc.Enums'),
        };
    }
}
