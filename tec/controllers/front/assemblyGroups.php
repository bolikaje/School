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
use ItPremium\TecDoc\Api\Type\AssemblyGroupFacetOptionsType;
use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Model\Data\LinkageTarget\LinkageTargetDetails;
use ItPremium\TecDoc\Model\Data\Manufacturer;
use ItPremium\TecDoc\Model\Data\ModelSeries;
use ItPremium\TecDoc\Model\Query\GetArticlesQuery;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Itp_TecdocAssemblyGroupsModuleFrontController extends TecDocFrontController
{
    /**
     * @var Manufacturer
     */
    private $manufacturer;

    /**
     * @var ModelSeries
     */
    private $modelSeries;

    /**
     * @var LinkageTargetDetails
     */
    private $vehicle;

    /**
     * @throws PrestaShopException
     * @throws MappingError
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws Symfony\Component\Cache\Exception\CacheException
     */
    public function init()
    {
        parent::init();

        $vehicleId = (int) Tools::getValue('vehicle_id');

        $this->vehicle = $this
            ->tecdoc
            ->vehicles()
            ->getVehicleById($vehicleId, $this->linkingTargetType);

        if (!Validate::isLoadedObject($this->vehicle)) {
            return $this->renderNotFound();
        }

        $this->manufacturer = $this
            ->tecdoc
            ->manufacturers()
            ->getManufacturerById($this->vehicle->manufacturerId, $this->linkingTargetType);

        if (!Validate::isLoadedObject($this->manufacturer) or !$this->manufacturer->active) {
            return $this->renderNotFound();
        }

        $this->modelSeries = $this
            ->tecdoc
            ->modelSeries()
            ->getModelSeriesById($this->manufacturer->id, $this->vehicle->modelSeriesId, $this->linkingTargetType);

        if (!Validate::isLoadedObject($this->modelSeries)) {
            return $this->renderNotFound();
        }

        $this->canonicalRedirection($this->vehicle->getLink());

        $assemblyGroupFacetOptionType = (new AssemblyGroupFacetOptionsType())
            ->setAssemblyGroupType($this->linkingTargetType->value);

        $getArticlesQuery = (new GetArticlesQuery())
            ->setAssemblyGroupFacetOptions($assemblyGroupFacetOptionType)
            ->setLinkageTargetId($vehicleId)
            ->setLinkageTargetType($this->linkingTargetType->value);

        $assemblyGroups = $this
            ->tecdoc
            ->assemblyGroups()
            ->getNestedAssemblyGroups($getArticlesQuery);

        $this->context->smarty->assign([
            'assembly_groups' => $assemblyGroups,
            'linking_target_type' => $this->linkingTargetType,
            'manufacturer' => $this->manufacturer,
            'model_series' => $this->modelSeries,
            'show_manufacturers_logo' => Configuration::get(ConfigurationConstant::TECDOC_SHOW_MANUFACTURERS_LOGO),
            'vehicle' => $this->vehicle,
        ]);

        $this->setTemplate('module:' . $this->module->name . '/views/templates/front/assembly-groups.tpl');
    }

    /**
     * @return array
     *
     * @throws PrestaShopException
     */
    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        if (!$this->notFound) {
            $breadcrumb['links'][] = [
                'title' => $this->manufacturer->name,
                'url' => $this->manufacturer->getLink($this->linkingTargetType),
            ];

            $breadcrumb['links'][] = [
                'title' => $this->modelSeries->name,
                'url' => $this->modelSeries->getLink(),
            ];

            $breadcrumb['links'][] = [
                'title' => $this->vehicle->description,
                'url' => $this->vehicle->getLink(),
            ];
        }

        return $breadcrumb;
    }

    /**
     * @return array
     */
    public function getTemplateVarPage(): array
    {
        $page = parent::getTemplateVarPage();

        if (!$this->notFound) {
            $title = Configuration::get(ConfigurationConstant::TECDOC_META_TITLE_FOR_ASSEMBLY_GROUPS, $this->context->language->id)
                ?: $this->trans('Choose group - %s %s %s', [$this->manufacturer->name, $this->modelSeries->name, $this->vehicle->description], 'Modules.Itptecdoc.Shop');

            $description = Configuration::get(ConfigurationConstant::TECDOC_META_DESCRIPTION_FOR_ASSEMBLY_GROUPS, $this->context->language->id);

            $page['body_classes']['tecdoc-assembly-groups-page'] = true;
            $page['title'] = $this->prepareMetaData($title);
            $page['meta']['title'] = $page['title'];
            $page['meta']['description'] = $this->prepareMetaData($description);
        }

        return $page;
    }

    /**
     * @return string
     *
     * @throws PrestaShopException
     */
    public function getCanonicalURL(): string
    {
        if ($this->notFound) {
            return '';
        }

        return $this->vehicle->getLink();
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function prepareMetaData(string $string): string
    {
        $search = [
            '%manufacturerName%',
            '%modelSeriesName%',
            '%vehicleName%',
        ];

        $replace = [
            $this->manufacturer->name,
            $this->modelSeries->name,
            $this->vehicle->description,
        ];

        return str_replace($search, $replace, $string);
    }
}
