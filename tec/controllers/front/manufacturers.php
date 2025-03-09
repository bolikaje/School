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
use ItPremium\TecDoc\Enum\LinkingTargetType;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Itp_TecdocManufacturersModuleFrontController extends TecDocFrontController
{
    /**
     * @throws GuzzleException
     * @throws MappingError
     * @throws PrestaShopException
     * @throws TecDocApiException
     */
    public function init()
    {
        parent::init();

        $manufacturersService = $this
            ->tecdoc
            ->manufacturers();

        $manufacturers = $manufacturersService
            ->getManufacturers(true, $this->linkingTargetType);

        $alphabeticalFilters = $manufacturersService
            ->generateAlphabeticalFilter($manufacturers);

        foreach ($manufacturers as $key => $manufacturer) {
            $manufacturerArr = $manufacturer->toArray();
            $manufacturerArr['image'] = $manufacturer->getImage();
            $manufacturerArr['link'] = $manufacturer->getLink($this->linkingTargetType);
            $manufacturers->set($key, $manufacturerArr);
        }

        Media::addJsDef([
            'manufacturers' => $manufacturers->toArray(),
        ]);

        $this->context->smarty->assign([
            'accessible_linking_target_types' => LinkingTargetType::getAccessibleLinkingTargetTypes(),
            'alphabetical_filters' => $alphabeticalFilters,
            'linking_target_type' => $this->linkingTargetType,
            'manufacturers' => $manufacturers,
            'show_alphabetical_filter' => Configuration::get(ConfigurationConstant::TECDOC_SHOW_MANUFACTURERS_ALPHABETICAL_FILTER),
            'show_manufacturers_logo' => Configuration::get(ConfigurationConstant::TECDOC_SHOW_MANUFACTURERS_LOGO),
        ]);

        $this->setTemplate('module:' . $this->module->name . '/views/templates/front/manufacturers.tpl');
    }

    /**
     * @return array
     */
    public function getTemplateVarPage(): array
    {
        $page = parent::getTemplateVarPage();

        $title = Configuration::get(ConfigurationConstant::TECDOC_META_TITLE_FOR_MANUFACTURERS, $this->context->language->id)
            ?: $this->trans('Manufacturers', [], 'Modules.Itptecdoc.Shop');

        $description = Configuration::get(ConfigurationConstant::TECDOC_META_DESCRIPTION_FOR_MANUFACTURERS, $this->context->language->id);

        $page['body_classes']['tecdoc-manufacturers-page'] = true;
        $page['title'] = $title;
        $page['meta']['title'] = $page['title'];
        $page['meta']['description'] = $description;

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

        return $this->context->link->getModuleLink($this->module->name, 'manufacturers', [
            'linking_target_type_slug' => $this->linkingTargetType->slug(),
        ], true);
    }
}
