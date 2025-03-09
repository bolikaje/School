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
use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Model\Data\Manufacturer;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Itp_TecdocModelSeriesModuleFrontController extends TecDocFrontController
{
    /**
     * @var Manufacturer
     */
    private $manufacturer;

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

        $this->manufacturer = $this->tecdoc
            ->manufacturers()
            ->getManufacturerBySlug($manufacturerSlug, $this->linkingTargetType);

        if (!Validate::isLoadedObject($this->manufacturer) or !$this->manufacturer->active) {
            return $this->renderNotFound();
        }

        $this->canonicalRedirection($this->manufacturer->getLink($this->linkingTargetType));

        if ($isGrouped = Configuration::get(ConfigurationConstant::TECDOC_GROUP_MODEL_SERIES)) {
            $modelSeries = $this
                ->tecdoc
                ->modelSeries()
                ->getGroupedModelSeries($this->manufacturer->id, $this->linkingTargetType);
        } else {
            $modelSeries = $this
                ->tecdoc
                ->modelSeries()
                ->getModelSeries($this->manufacturer->id, $this->linkingTargetType);
        }

        $this->context->smarty->assign([
            'is_grouped' => $isGrouped,
            'manufacturer' => $this->manufacturer,
            'model_series' => $modelSeries,
            'show_manufacturers_logo' => Configuration::get(ConfigurationConstant::TECDOC_SHOW_MANUFACTURERS_LOGO),
        ]);

        $this->setTemplate('module:' . $this->module->name . '/views/templates/front/models-series.tpl');
    }

    /**
     * @return array
     */
    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        if (!$this->notFound) {
            $breadcrumb['links'][] = [
                'title' => $this->manufacturer->name,
                'url' => $this->manufacturer->getLink($this->linkingTargetType),
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
            $title = Configuration::get(ConfigurationConstant::TECDOC_META_TITLE_FOR_MODEL_SERIES, $this->context->language->id)
                ?: $this->manufacturer->name;

            $description = Configuration::get(ConfigurationConstant::TECDOC_META_DESCRIPTION_FOR_MODEL_SERIES, $this->context->language->id);

            $page['body_classes']['tecdoc-model-series-page'] = true;
            $page['title'] = $this->prepareMetaData($title);
            $page['meta']['title'] = $page['title'];
            $page['meta']['description'] = $this->prepareMetaData($description);
        }

        return $page;
    }

    /**
     * @return string
     */
    public function getCanonicalURL(): string
    {
        if ($this->notFound) {
            return '';
        }

        return $this->manufacturer->getLink($this->linkingTargetType);
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
        ];

        $replace = [
            $this->manufacturer->name,
        ];

        return str_replace($search, $replace, $string);
    }
}
