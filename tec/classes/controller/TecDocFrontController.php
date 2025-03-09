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

use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Enum\LinkingTargetType;
use ItPremium\TecDoc\TecDoc;
use ItPremium\TecDoc\Utils\Helper;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class TecDocFrontController extends ModuleFrontController
{
    /**
     * @var bool
     */
    public $ssl = true;

    /**
     * @var bool
     */
    protected $notFound = false;

    /**
     * @var TecDoc
     */
    protected TecDoc $tecdoc;

    /**
     * @var LinkingTargetType
     */
    protected LinkingTargetType $linkingTargetType;

    /**
     * @throws PrestaShopException
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        $this->tecdoc = $this->get('it_premium.tecdoc');

        if (!$linkingTargetType = LinkingTargetType::getAccessibleLinkingTargetTypes()->first()) {
            exit($this->renderNotFound());
        }

        $this->linkingTargetType = LinkingTargetType::fromSlug(Tools::getValue('linking_target_type_slug')) ?? $linkingTargetType;
    }

    /**
     * @return void
     */
    protected function setAvailabilityFormVars(): void
    {
        $this->context->smarty->assign([
            'allow_availability_requests' => Configuration::get(ConfigurationConstant::TECDOC_ALLOW_AVAILABILITY_REQUESTS),
            'include_taxes' => (bool) Configuration::get(ConfigurationConstant::TECDOC_ID_TAX_RULES_GROUP),
            'recaptcha_enable' => Configuration::get(ConfigurationConstant::TECDOC_RECAPTCHA_ENABLE),
            'recaptcha_site_key' => Configuration::get(ConfigurationConstant::TECDOC_RECAPTCHA_SITE_KEY),
        ]);
    }

    /**
     * @return array
     */
    public function getBreadcrumbLinks(): array
    {
        $category = Helper::getTecDocCategory();

        $breadcrumb = parent::getBreadcrumbLinks();

        $breadcrumb['links'][] = [
            'title' => $category->name,
            'url' => $category->getLink(),
        ];

        return $breadcrumb;
    }

    /**
     * @return array
     */
    public function getTemplateVarPage(): array
    {
        $page = parent::getTemplateVarPage();

        if ($this->notFound) {
            $page['page_name'] = 'pagenotfound';
            $page['body_classes']['pagenotfound'] = true;
            $page['title'] = $this->trans('The page you are looking for was not found.', [], 'Shop.Theme.Global');
            $page['meta']['title'] = $this->trans('The page you are looking for was not found.', [], 'Shop.Theme.Global');
        }

        return $page;
    }

    /**
     * @return bool
     *
     * @throws PrestaShopException
     */
    public function renderNotFound(): bool
    {
        header('HTTP/1.1 404 Not Found');
        header('Status: 404 Not Found');
        $this->setTemplate('errors/404');
        $this->notFound = true;

        return false;
    }
}
