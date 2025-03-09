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

use ItPremium\TecDoc\Api\Type\LinkageTargetTypeAndId;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GetLinkageTargetsRequest extends AbstractTecDocRequest
{
    /**
     * @var string
     */
    protected string $linkageTargetCountry;

    /**
     * @var string
     */
    protected string $linkageTargetType = 'P';

    /**
     * @var LinkageTargetTypeAndId
     */
    protected LinkageTargetTypeAndId $linkageTargetIds;

    /**
     * @var string
     */
    protected string $query;

    /**
     * @var string
     */
    protected string $description;

    /**
     * @var int
     */
    protected int $mfrIds;

    /**
     * @var int
     */
    protected int $vehicleModelSeriesIds;

    /**
     * @var int
     */
    protected int $years;

    /**
     * @var string
     */
    protected string $engineCode;

    /**
     * @var int
     */
    protected int $capacityCCFrom;

    /**
     * @var int
     */
    protected int $capacityCCTo;

    /**
     * @var int
     */
    protected int $kiloWattsFrom;

    /**
     * @var int
     */
    protected int $kiloWattsTo;

    /**
     * @var int
     */
    protected int $perPage = 100;

    /**
     * @var int
     */
    protected int $page = 1;

    /**
     * @return string
     */
    public function getLinkageTargetCountry(): string
    {
        return $this->linkageTargetCountry;
    }

    /**
     * @param string $linkageTargetCountry
     *
     * @return $this
     */
    public function setLinkageTargetCountry(string $linkageTargetCountry): static
    {
        $this->linkageTargetCountry = $linkageTargetCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkageTargetType(): string
    {
        return $this->linkageTargetType;
    }

    /**
     * @param string $linkageTargetType
     *
     * @return $this
     */
    public function setLinkageTargetType(string $linkageTargetType): static
    {
        $this->linkageTargetType = $linkageTargetType;

        return $this;
    }

    /**
     * @return LinkageTargetTypeAndId
     */
    public function getLinkageTargetIds(): LinkageTargetTypeAndId
    {
        return $this->linkageTargetIds;
    }

    /**
     * @param LinkageTargetTypeAndId $linkageTargetTypeAndId
     *
     * @return $this
     */
    public function setLinkageTargetIds(LinkageTargetTypeAndId $linkageTargetTypeAndId): static
    {
        $this->linkageTargetIds = $linkageTargetTypeAndId;

        return $this;
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @param string $query
     *
     * @return $this
     */
    public function setQuery(string $query): static
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     *
     * @return $this
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return int
     */
    public function getMfrIds(): int
    {
        return $this->mfrIds;
    }

    /**
     * @param int $mfrIds
     *
     * @return $this
     */
    public function setMfrIds(int $mfrIds): static
    {
        $this->mfrIds = $mfrIds;

        return $this;
    }

    /**
     * @return int
     */
    public function getVehicleModelSeriesIds(): int
    {
        return $this->vehicleModelSeriesIds;
    }

    /**
     * @param int $vehicleModelSeriesIds
     *
     * @return $this
     */
    public function setVehicleModelSeriesIds(int $vehicleModelSeriesIds): static
    {
        $this->vehicleModelSeriesIds = $vehicleModelSeriesIds;

        return $this;
    }

    /**
     * @return int
     */
    public function getYears(): int
    {
        return $this->years;
    }

    /**
     * @param int $years
     *
     * @return $this
     */
    public function setYears(int $years): static
    {
        $this->years = $years;

        return $this;
    }

    /**
     * @return string
     */
    public function getEngineCode(): string
    {
        return $this->engineCode;
    }

    /**
     * @param string $engineCode
     *
     * @return $this
     */
    public function setEngineCode(string $engineCode): static
    {
        $this->engineCode = $engineCode;

        return $this;
    }

    /**
     * @return int
     */
    public function getCapacityCCFrom(): int
    {
        return $this->capacityCCFrom;
    }

    /**
     * @param int $capacityCCFrom
     *
     * @return $this
     */
    public function setCapacityCCFrom(int $capacityCCFrom): static
    {
        $this->capacityCCFrom = $capacityCCFrom;

        return $this;
    }

    /**
     * @return int
     */
    public function getCapacityCCTo(): int
    {
        return $this->capacityCCTo;
    }

    /**
     * @param int $capacityCCTo
     *
     * @return $this
     */
    public function setCapacityCCTo(int $capacityCCTo): static
    {
        $this->capacityCCTo = $capacityCCTo;

        return $this;
    }

    /**
     * @return int
     */
    public function getKiloWattsFrom(): int
    {
        return $this->kiloWattsFrom;
    }

    /**
     * @param int $kiloWattsFrom
     *
     * @return $this
     */
    public function setKiloWattsFrom(int $kiloWattsFrom): static
    {
        $this->kiloWattsFrom = $kiloWattsFrom;

        return $this;
    }

    /**
     * @return int
     */
    public function getKiloWattsTo(): int
    {
        return $this->kiloWattsTo;
    }

    /**
     * @param int $kiloWattsTo
     *
     * @return $this
     */
    public function setKiloWattsTo(int $kiloWattsTo): static
    {
        $this->kiloWattsTo = $kiloWattsTo;

        return $this;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     *
     * @return $this
     */
    public function setPerPage(int $perPage): static
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     *
     * @return $this
     */
    public function setPage(int $page): static
    {
        $this->page = $page;

        return $this;
    }
}
