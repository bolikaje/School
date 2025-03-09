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

class GetVehicleByIdsRequest extends AbstractTecDocRequest
{
    /**
     * @var string
     */
    protected string $articleCountry;

    /**
     * @var string
     */
    protected string $countriesCarSelection;

    /**
     * @var bool
     */
    protected bool $motorCodes = true;

    /**
     * @var int[]
     */
    protected array $carIds = [];

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
     * @return string
     */
    public function getCountriesCarSelection(): string
    {
        return $this->countriesCarSelection;
    }

    /**
     * @param string $countriesCarSelection
     *
     * @return $this
     */
    public function setCountriesCarSelection(string $countriesCarSelection): static
    {
        $this->countriesCarSelection = $countriesCarSelection;

        return $this;
    }

    /**
     * @return bool
     */
    public function isMotorCodes(): bool
    {
        return $this->motorCodes;
    }

    /**
     * @param bool $motorCodes
     *
     * @return $this
     */
    public function setMotorCodes(bool $motorCodes): static
    {
        $this->motorCodes = $motorCodes;

        return $this;
    }

    /**
     * @return array
     */
    public function getCarIds(): array
    {
        return $this->carIds;
    }

    /**
     * @param array $carIds
     *
     * @return $this
     */
    public function setCarIds(array $carIds): static
    {
        $this->carIds['array'] = $carIds;

        return $this;
    }
}
