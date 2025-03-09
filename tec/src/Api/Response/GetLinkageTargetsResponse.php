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

namespace ItPremium\TecDoc\Api\Response;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GetLinkageTargetsResponse extends AbstractTecDocResponse
{
    /**
     * @var int
     */
    protected int $total;

    /**
     * @var array
     */
    protected array $linkageTargets = [];

    /**
     * @var array
     */
    protected array $mfrFacets = [];

    /**
     * @var array
     */
    protected array $vehicleModelSeriesFacets = [];

    /**
     * @var array
     */
    protected array $hmdModelFacets = [];

    /**
     * @var array
     */
    protected array $yearFacets = [];

    /**
     * @var array
     */
    protected array $descriptionFacets = [];

    /**
     * @var array
     */
    protected array $linkageTargetTypeFacets = [];

    /**
     * @return int
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * @param int $total
     *
     * @return $this
     */
    public function setTotal(int $total): static
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return array
     */
    public function getLinkageTargets(): array
    {
        return $this->linkageTargets;
    }

    /**
     * @param array $linkageTargets
     *
     * @return $this
     */
    public function setLinkageTargets(array $linkageTargets): static
    {
        $this->linkageTargets = $linkageTargets;

        return $this;
    }

    /**
     * @return array
     */
    public function getMfrFacets(): array
    {
        return $this->mfrFacets;
    }

    /**
     * @param array $mfrFacets
     *
     * @return $this
     */
    public function setMfrFacets(array $mfrFacets): static
    {
        $this->mfrFacets = $mfrFacets;

        return $this;
    }

    /**
     * @return array
     */
    public function getVehicleModelSeriesFacets(): array
    {
        return $this->vehicleModelSeriesFacets;
    }

    /**
     * @param array $vehicleModelSeriesFacets
     *
     * @return $this
     */
    public function setVehicleModelSeriesFacets(array $vehicleModelSeriesFacets): static
    {
        $this->vehicleModelSeriesFacets = $vehicleModelSeriesFacets;

        return $this;
    }

    /**
     * @return array
     */
    public function getHmdModelFacets(): array
    {
        return $this->hmdModelFacets;
    }

    /**
     * @param array $hmdModelFacets
     *
     * @return $this
     */
    public function setHmdModelFacets(array $hmdModelFacets): static
    {
        $this->hmdModelFacets = $hmdModelFacets;

        return $this;
    }

    /**
     * @return array
     */
    public function getYearFacets(): array
    {
        return $this->yearFacets;
    }

    /**
     * @param array $yearFacets
     *
     * @return $this
     */
    public function setYearFacets(array $yearFacets): static
    {
        $this->yearFacets = $yearFacets;

        return $this;
    }

    /**
     * @return array
     */
    public function getDescriptionFacets(): array
    {
        return $this->descriptionFacets;
    }

    /**
     * @param array $descriptionFacets
     *
     * @return $this
     */
    public function setDescriptionFacets(array $descriptionFacets): static
    {
        $this->descriptionFacets = $descriptionFacets;

        return $this;
    }

    /**
     * @return array
     */
    public function getLinkageTargetTypeFacets(): array
    {
        return $this->linkageTargetTypeFacets;
    }

    /**
     * @param array $linkageTargetTypeFacets
     *
     * @return $this
     */
    public function setLinkageTargetTypeFacets(array $linkageTargetTypeFacets): static
    {
        $this->linkageTargetTypeFacets = $linkageTargetTypeFacets;

        return $this;
    }
}
