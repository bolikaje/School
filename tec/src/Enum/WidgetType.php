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

enum WidgetType: int implements HasLabelInterface
{
    use EnumToArray;
    use HasTranslations;

    case SEARCH_FORM = 0;
    case VEHICLE_SEARCH = 1;
    case MANUFACTURERS_LIST = 2;
    case BRANDS_LIST = 3;
    case TECDOC_INSIDE = 4;
    case CUSTOM_HTML = 5;
    case ASSEMBLY_GROUPS_LIST = 6;

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::ASSEMBLY_GROUPS_LIST => self::getTranslator()->trans('Assembly groups', [], 'Modules.Itptecdoc.Enums'),
            self::BRANDS_LIST => self::getTranslator()->trans('Brands list', [], 'Modules.Itptecdoc.Enums'),
            self::CUSTOM_HTML => self::getTranslator()->trans('Custom HTML', [], 'Modules.Itptecdoc.Enums'),
            self::MANUFACTURERS_LIST => self::getTranslator()->trans('Manufacturers list', [], 'Modules.Itptecdoc.Enums'),
            self::SEARCH_FORM => self::getTranslator()->trans('Search form', [], 'Modules.Itptecdoc.Enums'),
            self::TECDOC_INSIDE => self::getTranslator()->trans('TecDoc inside', [], 'Modules.Itptecdoc.Enums'),
            self::VEHICLE_SEARCH => self::getTranslator()->trans('Vehicle search', [], 'Modules.Itptecdoc.Enums'),
        };
    }
}
