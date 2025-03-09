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

namespace ItPremium\TecDoc\Model;

use ItPremium\TecDoc\Utils\Helper;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class StatisticData
{
    /**
     * StatisticData constructor.
     *
     * @param int $ordersCount
     * @param float $revenueTaxExcl
     * @param float $revenueTaxIncl
     */
    public function __construct(
        private readonly int $ordersCount,
        private readonly float $revenueTaxExcl,
        private readonly float $revenueTaxIncl,
    ) {
    }

    /**
     * @return int
     */
    public function getOrdersCount(): int
    {
        return $this->ordersCount;
    }

    /**
     * @return float
     */
    public function getRevenueTaxExcl(): float
    {
        return $this->revenueTaxExcl;
    }

    /**
     * @return float
     */
    public function getRevenueTaxIncl(): float
    {
        return $this->revenueTaxIncl;
    }

    /**
     * @return string
     *
     * @throws LocalizationException
     */
    public function getRevenueTaxExclFormatted(): string
    {
        return Helper::formatPrice($this->revenueTaxExcl);
    }

    /**
     * @return string
     *
     * @throws LocalizationException
     */
    public function getRevenueTaxInclFormatted(): string
    {
        return Helper::formatPrice($this->revenueTaxIncl);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'orders_count' => $this->ordersCount,
            'revenue_tax_excl' => $this->revenueTaxExcl,
            'revenue_tax_incl' => $this->revenueTaxIncl,
        ];
    }
}
