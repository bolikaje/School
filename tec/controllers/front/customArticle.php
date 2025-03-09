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
use ItPremium\TecDoc\Model\Data\Article\CustomArticle;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Itp_TecdocCustomArticleModuleFrontController extends Itp_TecdocArticleModuleFrontController
{
    /**
     * @var CustomArticle
     */
    protected $article;

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws MappingError
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws Symfony\Component\Cache\Exception\CacheException
     */
    public function init()
    {
        parent::init();

        $customArticleId = (string) Tools::getValue('id_custom_article');

        $this->article = $this
            ->tecdoc
            ->articles()
            ->getSingleCustomArticle($customArticleId);

        if (!$this->article) {
            return $this->renderNotFound();
        }

        /*
         * Attempt redirect to TecDoc article if it exists
         */
        if ($this->article->brandId) {
            $tecdocArticle = $this
                ->tecdoc
                ->articles()
                ->getSingleArticle($this->article->brandId, $this->article->reference);

            if ($tecdocArticle) {
                $this->canonicalRedirection($this->article->getLink());
            }
        }

        $this->canonicalRedirection($this->article->getLink());

        $this->context->smarty->assign([
            'article' => $this->article,
        ]);

        $this->setTemplate('module:' . $this->module->name . '/views/templates/front/article.tpl');
    }

    /**
     * @return array
     */
    public function getTemplateVarPage(): array
    {
        $page = parent::getTemplateVarPage();

        if (!$this->notFound) {
            $title = Configuration::get(ConfigurationConstant::TECDOC_META_TITLE_FOR_CUSTOM_ARTICLE, $this->context->language->id)
                ?: '%name% %brand% %reference%';

            $description = Configuration::get(ConfigurationConstant::TECDOC_META_DESCRIPTION_FOR_CUSTOM_ARTICLE, $this->context->language->id);

            $page['body_classes']['tecdoc-article-page'] = true;
            $page['title'] = $this->prepareMetaData($title);
            $page['meta']['title'] = $page['title'];
            $page['meta']['description'] = $this->prepareMetaData($description);
        }

        return $page;
    }
}
