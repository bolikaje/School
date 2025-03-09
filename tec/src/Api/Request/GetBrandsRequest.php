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

namespace ItPremium\TecDoc\Api\Request;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GetBrandsRequest extends AbstractTecDocRequest
{
    /**
     * @var string
     */
    protected string $articleCountry;

    /**
     * @var bool
     */
    protected bool $includeAddressDetails = true;

    /**
     * @var bool
     */
    protected bool $includeDataSupplierLogo = true;

    /**
     * @return string
     */
    public function getArticleCountry(): string
    {
        return $this->articleCountry;
    }

    /**
     * @param string $articleCountry
     *
     * @return $this
     */
    public function setArticleCountry(string $articleCountry): static
    {
        $this->articleCountry = $articleCountry;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIncludeAddressDetails(): bool
    {
        return $this->includeAddressDetails;
    }

    /**
     * @param bool $includeAddressDetails
     *
     * @return $this
     */
    public function setIncludeAddressDetails(bool $includeAddressDetails): static
    {
        $this->includeAddressDetails = $includeAddressDetails;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIncludeDataSupplierLogo(): bool
    {
        return $this->includeDataSupplierLogo;
    }

    /**
     * @param bool $includeDataSupplierLogo
     *
     * @return $this
     */
    public function setIncludeDataSupplierLogo(bool $includeDataSupplierLogo): static
    {
        $this->includeDataSupplierLogo = $includeDataSupplierLogo;

        return $this;
    }
}
