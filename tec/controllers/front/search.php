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
use ItPremium\TecDoc\Enum\NumberType;
use ItPremium\TecDoc\Enum\SearchType;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Itp_TecdocSearchModuleFrontController extends TecDocArticleListingFrontController
{
    /**
     * @var string
     */
    private $searchQuery;

    /**
     * @var SearchType
     */
    private $searchType;

    /**
     * @var NumberType
     */
    private $numberType;

    /**
     * @throws GuzzleException
     * @throws MappingError
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws TecDocApiException
     * @throws Symfony\Component\Cache\Exception\CacheException
     */
    public function init()
    {
        parent::init();

        $this->searchQuery = trim((string) Tools::getValue('search_query'));
        $this->searchType = SearchType::tryFrom(Tools::getValue('search_type')) ?? SearchType::TECDOC;

        if ($this->searchType == SearchType::PRESTASHOP) {
            Tools::redirect(
                $this->context->link->getPageLink('search', true, null, [
                    'search_query' => $this->searchQuery,
                    'search_type' => $this->searchType->value,
                ])
            );
        }

        if (!$this->searchQuery) {
            exit($this->renderNotFound());
        }

        $this->numberType = NumberType::tryFrom(
            Configuration::get(ConfigurationConstant::TECDOC_SEARCH_NUMBER_TYPE)
        ) ?? NumberType::ANY_NUMBER;

        $this->articles = $this
            ->tecdoc
            ->articles()
            ->getArticlesByKeyword(
                keyword: $this->searchQuery,
                numberType: $this->numberType
            );

        $this->context->smarty->assign([
            'search_query' => $this->searchQuery,
        ]);
    }

    /**
     * @return array
     */
    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        if ($this->searchQuery) {
            $breadcrumb['links'][] = [
                'title' => $this->trans('Search - %s', [$this->searchQuery], 'Modules.Itptecdoc.Breadcrumbs'),
                'url' => false,
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
            $page['body_classes']['tecdoc-search-page'] = true;
            $page['title'] = $this->trans('Search - %s', [$this->searchQuery], 'Modules.Itptecdoc.Breadcrumbs');
            $page['meta']['title'] = $page['title'];
        }

        return $page;
    }
}
