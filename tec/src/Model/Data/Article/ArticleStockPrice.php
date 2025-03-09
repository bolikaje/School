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

namespace ItPremium\TecDoc\Model\Data\Article;

use Doctrine\Common\Collections\ArrayCollection;
use ItPremium\TecDoc\Utils\Helper;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class ArticleStockPrice
{
    /**
     * ArticleStockPrice constructor.
     *
     * @param ?float $wholesalePrice
     * @param float $priceWithoutReductionsWithoutTax
     * @param float $priceWithReductionsWithoutTax
     * @param float $priceWithoutReductionsWithTax
     * @param float $priceWithReductionsWithTax
     * @param ?float $depositWithoutTax
     * @param ?float $depositWithTax
     * @param float $displayedPriceWithoutReductions
     * @param float $displayedPriceWithReductions
     * @param ?float $displayedDeposit
     * @param float $displayedDiscountRate
     * @param ArrayCollection $groupDiscounts
     */
    public function __construct(
        /** @var float */
        public readonly ?float $wholesalePrice,

        /** @var float */
        public readonly float $priceWithoutReductionsWithoutTax,

        /** @var float */
        public readonly float $priceWithReductionsWithoutTax,

        /** @var float */
        public readonly float $priceWithoutReductionsWithTax,

        /** @var float */
        public readonly float $priceWithReductionsWithTax,

        /** @var float */
        public readonly ?float $depositWithoutTax,

        /** @var float */
        public readonly ?float $depositWithTax,

        /** @var float */
        public readonly float $displayedPriceWithoutReductions,

        /** @var float */
        public readonly float $displayedPriceWithReductions,

        /** @var float */
        public readonly ?float $displayedDeposit,

        /** @var float */
        public readonly float $displayedDiscountRate,

        /** @var ArrayCollection<int, ArticleStockGroupDiscount> */
        public readonly ArrayCollection $groupDiscounts = new ArrayCollection(),
    ) {
    }

    /**
     * @return string
     *
     * @throws LocalizationException
     */
    public function getDisplayedPriceWithoutReductionsFormatted(): string
    {
        return Helper::formatPrice($this->displayedPriceWithoutReductions);
    }

    /**
     * @return string
     *
     * @throws LocalizationException
     */
    public function getDisplayedPriceWithReductionsFormatted(): string
    {
        return Helper::formatPrice($this->displayedPriceWithReductions);
    }

    /**
     * @return string
     *
     * @throws LocalizationException
     */
    public function getDisplayedDepositFormatted(): string
    {
        return Helper::formatPrice($this->displayedDeposit);
    }

    /**
     * @return string
     */
    public function getDisplayedDiscountRateFormatted(): string
    {
        return $this->displayedDiscountRate . '%';
    }
}
