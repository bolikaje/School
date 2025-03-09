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

class GetVehicleIdsByCriteriaRequest extends AbstractTecDocRequest
{
    /**
     * @var string
     */
    protected string $carType;

    /**
     * @var string
     */
    protected string $countriesCarSelection;

    /**
     * @var int
     */
    protected int $manuId;

    /**
     * @var int
     */
    protected int $modId;

    /**
     * @return string
     */
    public function getCarType(): string
    {
        return $this->carType;
    }

    /**
     * @param string $carType
     *
     * @return $this
     */
    public function setCarType(string $carType): static
    {
        $this->carType = $carType;

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
     * @return int
     */
    public function getManuId(): int
    {
        return $this->manuId;
    }

    /**
     * @param int $manuId
     *
     * @return $this
     */
    public function setManuId(int $manuId): static
    {
        $this->manuId = $manuId;

        return $this;
    }

    /**
     * @return int
     */
    public function getModId(): int
    {
        return $this->modId;
    }

    /**
     * @param int $modId
     *
     * @return $this
     */
    public function setModId(int $modId): static
    {
        $this->modId = $modId;

        return $this;
    }
}
