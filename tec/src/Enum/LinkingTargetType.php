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

use Doctrine\Common\Collections\ArrayCollection;
use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Enum\Trait\HasTranslations;

if (!defined('_PS_VERSION_')) {
    exit;
}

enum LinkingTargetType: string
{
    use HasTranslations;

    /*
     * These are the linking target types that are available in the TecDoc API.
     *
     * 'P': Vehicle Type (Passenger + Motorcycle + LCV),
     * 'V': Passenger Car,
     * 'L': LCV,
     * 'B': Motorcycle,
     * 'O':CV Type (Commercial Vehicle + Tractor),
     * 'C': Commercial Vehicle,
     * 'T': Tractor,
     * 'M': Engine,
     * 'A': Axle,
     * 'K': CV Body Type,
     * 'H': HMD Vehicle,
     * 'S': Vehicle Model Series
     */

    case PASSENGER = 'P';
    case COMMERCIAL = 'O';
    case MOTORCYCLE = 'B';

    /**
     * @return string
     */
    public function alternativeValue(): string
    {
        return match ($this) {
            self::PASSENGER => 'VL',
            default => $this->value,
        };
    }

    /**
     * @return string
     */
    public function css(): string
    {
        return match ($this) {
            self::PASSENGER => 'passenger',
            self::COMMERCIAL => 'commercial',
            self::MOTORCYCLE => 'motorcycle',
        };
    }

    /**
     * @return string
     */
    public function label(): string
    {
        return match ($this) {
            self::PASSENGER => self::getTranslator()->trans('Passenger vehicles', [], 'Modules.Itptecdoc.Enums'),
            self::COMMERCIAL => self::getTranslator()->trans('Commercial vehicles', [], 'Modules.Itptecdoc.Enums'),
            self::MOTORCYCLE => self::getTranslator()->trans('Motorcycles', [], 'Modules.Itptecdoc.Enums'),
        };
    }

    /**
     * @return string
     */
    public function slug(): string
    {
        return self::slugMap()[$this->value];
    }

    /**
     * @param string $slug
     *
     * @return ?LinkingTargetType
     */
    public static function fromSlug(string $slug): ?LinkingTargetType
    {
        $enumValue = array_search($slug, self::slugMap(), true);

        return ($enumValue !== false) ? self::tryFrom($enumValue) : null;
    }

    /**
     * @return string[]
     */
    private static function slugMap(): array
    {
        return [
            self::PASSENGER->value => 'passenger',
            self::COMMERCIAL->value => 'commercial',
            self::MOTORCYCLE->value => 'motorcycle',
        ];
    }

    /**
     * @return ArrayCollection<int, LinkingTargetType>
     */
    public static function getAccessibleLinkingTargetTypes(): ArrayCollection
    {
        $availableLinkingTargetTypes = new ArrayCollection();

        if (\Configuration::get(ConfigurationConstant::TECDOC_SHOW_PASSENGER_VEHICLES)) {
            $availableLinkingTargetTypes->add(LinkingTargetType::PASSENGER);
        }

        if (\Configuration::get(ConfigurationConstant::TECDOC_SHOW_COMMERCIAL_VEHICLES)) {
            $availableLinkingTargetTypes->add(LinkingTargetType::COMMERCIAL);
        }

        if (\Configuration::get(ConfigurationConstant::TECDOC_SHOW_MOTORCYCLES)) {
            $availableLinkingTargetTypes->add(LinkingTargetType::MOTORCYCLE);
        }

        return $availableLinkingTargetTypes;
    }
}
