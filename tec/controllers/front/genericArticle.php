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
use ItPremium\TecDoc\Model\Data\GenericArticle;
use ItPremium\TecDoc\Model\Query\GetArticlesQuery;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Itp_TecdocGenericArticleModuleFrontController extends TecDocArticleListingFrontController
{
    /**
     * @var GenericArticle
     */
    private $genericArticle;

    /**
     * @throws GuzzleException
     * @throws MappingError
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws TecDocApiException
     * @throws Symfony\Component\Cache\Exception\CacheException
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        $genericArticleId = (int) Tools::getValue('generic_article_id');

        $this->genericArticle = $this
            ->tecdoc
            ->genericArticles()
            ->getGenericArticleById($genericArticleId);

        if (!Validate::isLoadedObject($this->genericArticle)) {
            exit($this->renderNotFound());
        }

        $this->canonicalRedirection($this->genericArticle->getLink());

        $getArticlesQuery = new GetArticlesQuery();
        $getArticlesQuery->setGenericArticleIds($this->genericArticle->id);

        $this->articles = $this
            ->tecdoc
            ->articles()
            ->getArticles($getArticlesQuery, true);
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
                'title' => $this->genericArticle->designation,
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
            $title = Configuration::get(ConfigurationConstant::TECDOC_META_TITLE_FOR_GENERIC_ARTICLE, $this->context->language->id)
                ?: $this->genericArticle->designation;

            $description = Configuration::get(ConfigurationConstant::TECDOC_META_DESCRIPTION_FOR_GENERIC_ARTICLE, $this->context->language->id);

            $page['body_classes']['tecdoc-generic-article-page'] = true;
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

        return $this->genericArticle->getLink();
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function prepareMetaData(string $string): string
    {
        $search = [
            '%genericArticleName%',
        ];

        $replace = [
            $this->genericArticle->designation,
        ];

        return str_replace($search, $replace, $string);
    }
}
