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

use ItPremium\TecDoc\Enum\Interface\HasLabelInterface;
use ItPremium\TecDoc\Enum\Trait\EnumToArray;
use ItPremium\TecDoc\Enum\Trait\HasTranslations;

if (!defined('_PS_VERSION_')) {
    exit;
}

enum BrandQuality: int implements HasLabelInterface
{
    use EnumToArray;
    use HasTranslations;

    case NONE = 0;
    case ECONOMY = 1;
    case MEDIUM = 2;
    case PREMIUM = 3;
    case OEM = 4;

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::NONE => self::getTranslator()->trans('None', [], 'Modules.Itptecdoc.Enums'),
            self::ECONOMY => self::getTranslator()->trans('Economy', [], 'Modules.Itptecdoc.Enums'),
            self::MEDIUM => self::getTranslator()->trans('Medium', [], 'Modules.Itptecdoc.Enums'),
            self::PREMIUM => self::getTranslator()->trans('Premium', [], 'Modules.Itptecdoc.Enums'),
            self::OEM => self::getTranslator()->trans('OEM', [], 'Modules.Itptecdoc.Enums'),
        };
    }

    /**
     * @return string
     */
    public function css(): string
    {
        return match ($this) {
            self::NONE => 'none',
            self::ECONOMY => 'economy',
            self::MEDIUM => 'medium',
            self::PREMIUM => 'premium',
            self::OEM => 'original',
        };
    }
}
