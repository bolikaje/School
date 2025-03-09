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

use CuyZ\Valinor\Mapper\MappingError;
use GuzzleHttp\Exception\GuzzleException;
use ItPremium\TecDoc\Api\Exception\TecDocApiException;
use ItPremium\TecDoc\Enum\LinkingTargetType;
use ItPremium\TecDoc\Model\Query\GetLinkageTargetsQuery;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Itp_TecdocVehicleSearchModuleFrontController extends TecDocFrontController
{
    /**
     * @var int
     */
    private $manufacturerId;

    /**
     * @var int
     */
    private $modelSeriesId;

    /**
     * @var int
     */
    private $vehicleId;

    /**
     * @var int
     */
    private $assemblyGroupId;

    /**
     * @return void
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws PrestaShopException
     * @throws TecDocApiException
     * @throws Symfony\Component\Cache\Exception\CacheException
     */
    public function init()
    {
        parent::init();

        $this->manufacturerId = (int) Tools::getValue('manufacturer_id', 0);
        $this->modelSeriesId = (int) Tools::getValue('model_series_id', 0);
        $this->vehicleId = (int) Tools::getValue('vehicle_id', 0);
        $this->assemblyGroupId = (int) Tools::getValue('assembly_group_id', 0);

        $this->linkingTargetType = LinkingTargetType::tryFrom(Tools::getValue('linking_target_type'))
            ?? $this->linkingTargetType;

        if (!$this->ajax) {
            $redirectUrl = $this->context->link->getModuleLink($this->module->name, 'manufacturers', [
                'linking_target_type_slug' => $this->linkingTargetType->slug(),
            ], true);

            if ($this->assemblyGroupId and $this->vehicleId) {
                $assemblyGroup = $this
                    ->tecdoc
                    ->assemblyGroups()
                    ->getAssemblyGroupById($this->assemblyGroupId);

                $vehicle = $this
                    ->tecdoc
                    ->vehicles()
                    ->getVehicleById($this->vehicleId, $this->linkingTargetType);

                if ($assemblyGroup) {
                    $redirectUrl = $assemblyGroup->getLink($vehicle);
                }
            } elseif ($this->vehicleId) {
                $vehicle = $this
                    ->tecdoc
                    ->vehicles()
                    ->getVehicleById($this->vehicleId, $this->linkingTargetType);

                if ($vehicle) {
                    $redirectUrl = $vehicle->getLink();
                }
            } elseif ($this->manufacturerId and $this->modelSeriesId) {
                $modelSeries = $this
                    ->tecdoc
                    ->modelSeries()
                    ->getModelSeriesById($this->manufacturerId, $this->modelSeriesId, $this->linkingTargetType);

                if ($modelSeries) {
                    $redirectUrl = $modelSeries->getLink();
                }
            } elseif ($this->manufacturerId) {
                $manufacturer = $this->tecdoc
                    ->manufacturers()
                    ->getManufacturerById($this->manufacturerId, $this->linkingTargetType);

                if ($manufacturer) {
                    $redirectUrl = $manufacturer->getLink($this->linkingTargetType);
                }
            }

            Tools::redirect($redirectUrl);
        }
    }

    /**
     * @throws GuzzleException
     * @throws MappingError
     * @throws PrestaShopException
     * @throws TecDocApiException
     */
    public function displayAjaxManufacturers(): void
    {
        header('Content-Type: application/json');

        $manufacturers = $this
            ->tecdoc
            ->manufacturers()
            ->getManufacturers(linkingTargetType: $this->linkingTargetType);

        $this->ajaxRender(json_encode([
            'manufacturers' => $manufacturers->toArray(),
        ]));
    }

    /**
     * @throws GuzzleException
     * @throws MappingError
     * @throws PrestaShopException
     * @throws TecDocApiException
     * @throws Symfony\Component\Cache\Exception\CacheException
     */
    public function displayAjaxModelSeries(): void
    {
        header('Content-Type: application/json');

        $groupedModelSeries = $this
            ->tecdoc
            ->modelSeries()
            ->getModelSeries($this->manufacturerId, $this->linkingTargetType);

        $this->ajaxRender(json_encode([
            'model_series' => $groupedModelSeries->toArray(),
        ]));
    }

    /**
     * @throws PrestaShopException
     * @throws MappingError
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws Symfony\Component\Cache\Exception\CacheException
     */
    public function displayAjaxVehicles(): void
    {
        header('Content-Type: application/json');

        $getLinkageTargetsQuery = (new GetLinkageTargetsQuery())
            ->setLinkageTargetType($this->linkingTargetType->value)
            ->setVehicleModelSeriesIds($this->modelSeriesId);

        $vehicles = $this
            ->tecdoc
            ->vehicles()
            ->getLinkageTargets($getLinkageTargetsQuery);

        $this->ajaxRender(json_encode([
            'vehicles' => $vehicles->toArray(),
        ]));
    }
}
