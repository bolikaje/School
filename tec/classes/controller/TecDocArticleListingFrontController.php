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

use Doctrine\Common\Collections\ArrayCollection;
use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Enum\SortOrder;
use ItPremium\TecDoc\Model\Data\Article\Article;
use ItPremium\TecDoc\Utils\Helper;
use PrestaShop\PrestaShop\Core\Product\Search\Pagination;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class TecDocArticleListingFrontController extends TecDocFrontController
{
    /**
     * @var ArrayCollection
     */
    protected ArrayCollection $articles;

    /**
     * @var SortOrder
     */
    protected SortOrder $sortOrder;

    /**
     * @var int
     */
    protected int $page;

    /**
     * @var int
     */
    protected int $perPage;

    /**
     * @throws PrestaShopException
     */
    public function init()
    {
        parent::init();

        $this->sortOrder = SortOrder::tryFrom(Tools::getValue('sort_order')) ?? SortOrder::CHEAPEST;
        $this->page = (int) Tools::getValue('page') ?: 1;
        $this->perPage = (int) Configuration::get(
            key: ConfigurationConstant::TECDOC_ARTICLES_PER_PAGE,
            default: 10
        );
    }

    /**
     * @return void
     *
     * @throws PrestaShopException
     * @throws Exception
     */
    public function initContent(): void
    {
        parent::initContent();

        if (!$this->notFound) {
            $facetService = $this->tecdoc->facets();

            if ($showFacets = Configuration::get(ConfigurationConstant::TECDOC_SHOW_FACETS)) {
                $facetService->init($this->articles);

                if ($activeFilters = Tools::getValue('filters')) {
                    foreach ($activeFilters as $inputName => $values) {
                        $facetService->addActiveFilter($inputName, $values);
                    }

                    $this->articles = $facetService->filterArticles();
                }

                $facetService->generateFacets();
            }

            $this->articles = $facetService->sort($this->articles, $this->sortOrder);

            $this->setAvailabilityFormVars();

            $facets = $facetService->getFacets();

            Media::addJsDef([
                'facets' => json_decode(Helper::serializeObjectToJson($facets)),
            ]);

            $this->context->smarty->assign([
                'articles' => $this->paginate(),
                'facets' => $facets,
                'pagination' => $this->getTemplateVarPagination(),
                'show_facets' => $showFacets and $facets,
                'show_facets_count' => Configuration::get(ConfigurationConstant::TECDOC_SHOW_FACETS_COUNT),
                'sort_order' => $this->sortOrder,
            ]);

            $this->setTemplate('module:' . $this->module->name . '/views/templates/front/articles.tpl');
        }
    }

    /**
     * @return string
     */
    public function getLayout(): string
    {
        if ($this->articles->isEmpty()) {
            return $this->context->shop->theme->getLayoutPath('layout-full-width');
        }

        return parent::getLayout();
    }

    /**
     * @return ArrayCollection<int, Article>
     */
    protected function paginate(): ArrayCollection
    {
        $articles = $this->articles->slice(
            $this->perPage * ($this->page - 1),
            $this->perPage
        );

        return new ArrayCollection($articles);
    }

    /**
     * @return array
     */
    protected function getTemplateVarPagination(): array
    {
        $totalItems = $this->articles->count();

        $pagination = new Pagination();
        $pagination
            ->setPage($this->page)
            ->setPagesCount((int) ceil($totalItems / $this->perPage));

        $itemsShownFrom = ($this->perPage * ($this->page - 1)) + 1;
        $itemsShownTo = $this->perPage * $this->page;

        $pages = array_map(function ($link) {
            $link['url'] = $this->updateQueryString([
                'page' => $link['page'] > 1 ? $link['page'] : null,
            ]);

            return $link;
        }, $pagination->buildLinks());

        $pages = array_filter($pages, function ($page) use ($pagination) {
            if ('previous' === $page['type'] and 1 === $pagination->getPage()) {
                return false;
            }

            if ('next' === $page['type'] and $pagination->getPagesCount() === $pagination->getPage()) {
                return false;
            }

            return true;
        });

        return [
            'current_page' => $pagination->getPage(),
            'items_shown_from' => $itemsShownFrom,
            'items_shown_to' => ($itemsShownTo <= $totalItems) ? $itemsShownTo : $totalItems,
            'pages' => $pages,
            'pages_count' => $pagination->getPagesCount(),
            'should_be_displayed' => (count($pagination->buildLinks()) > 3),
            'total_items' => $totalItems,
        ];
    }
}
