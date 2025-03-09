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
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Exception\GuzzleException;
use ItPremium\TecDoc\Api\Exception\TecDocApiException;
use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Model\Data\LinkageTarget\LinkageTargetDetails;
use ItPremium\TecDoc\Model\Data\Manufacturer;
use ItPremium\TecDoc\Model\Data\ModelSeries;
use ItPremium\TecDoc\Model\Query\GetLinkageTargetsQuery;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Itp_TecdocVehiclesModuleFrontController extends TecDocFrontController
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
     * @var ArrayCollection<int, LinkageTargetDetails>
     */
    private $vehicles;

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

        $manufacturerSlug = (string) Tools::getValue('manufacturer_slug');
        $modelSeriesId = (int) Tools::getValue('model_series_id', 0);

        $this->manufacturer = $this
            ->tecdoc
            ->manufacturers()
            ->getManufacturerBySlug($manufacturerSlug, $this->linkingTargetType);

        if (!Validate::isLoadedObject($this->manufacturer) or !$this->manufacturer->active) {
            return $this->renderNotFound();
        }

        $this->modelSeries = $this
            ->tecdoc
            ->modelSeries()
            ->getModelSeriesById($this->manufacturer->id, $modelSeriesId, $this->linkingTargetType);

        if (!Validate::isLoadedObject($this->modelSeries)) {
            return $this->renderNotFound();
        }

        $this->canonicalRedirection($this->modelSeries->getLink());

        $getLinkageTargetsQuery = (new GetLinkageTargetsQuery())
            ->setLinkageTargetType($this->linkingTargetType->value)
            ->setVehicleModelSeriesIds($modelSeriesId);

        $this->vehicles = $this
            ->tecdoc
            ->vehicles()
            ->getLinkageTargets($getLinkageTargetsQuery);

        $this->context->smarty->assign([
            'manufacturer' => $this->manufacturer,
            'model_series' => $this->modelSeries,
            'show_manufacturers_logo' => Configuration::get(ConfigurationConstant::TECDOC_SHOW_MANUFACTURERS_LOGO),
            'vehicles' => $this->vehicles,
        ]);

        $this->setTemplate('module:' . $this->module->name . '/views/templates/front/vehicles.tpl');
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
            $title = Configuration::get(ConfigurationConstant::TECDOC_META_TITLE_FOR_VEHICLES, $this->context->language->id)
                ?: '%manufacturerName% %modelSeriesName%';

            $description = Configuration::get(ConfigurationConstant::TECDOC_META_DESCRIPTION_FOR_VEHICLES, $this->context->language->id);

            $page['body_classes']['tecdoc-vehicles-page'] = true;
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

        return $this->modelSeries->getLink();
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
        ];

        $replace = [
            $this->manufacturer->name,
            $this->modelSeries->name,
        ];

        return str_replace($search, $replace, $string);
    }
}
