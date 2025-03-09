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
use ItPremium\TecDoc\Model\Data\Article\Article;
use ItPremium\TecDoc\Utils\Helper;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Itp_TecdocArticleModuleFrontController extends TecDocFrontController
{
    /**
     * @var Article
     */
    protected $article;

    /**
     * @throws GuzzleException
     * @throws MappingError
     * @throws PrestaShopException
     * @throws TecDocApiException
     * @throws Symfony\Component\Cache\Exception\CacheException
     */
    public function init()
    {
        parent::init();

        $brandSlug = (string) Tools::getValue('brand_slug');
        $reference = urldecode(
            (string) Tools::getValue('reference')
        );

        if ($brandSlug and $reference) {
            $brand = $this
                ->tecdoc
                ->brands()
                ->getBrandBySlug($brandSlug);

            if (!$brand) {
                return $this->renderNotFound();
            }

            $this->article = $this
                ->tecdoc
                ->articles()
                ->getSingleArticle($brand->id, $reference);

            if (!$this->article) {
                return $this->renderNotFound();
            }

            $this->canonicalRedirection($this->article->getLink());

            $linkedManufacturers = $this
                ->tecdoc
                ->manufacturers()
                ->getArticleLinkedManufacturers($this->article->getId());

            $this->setAvailabilityFormVars();

            Media::addJsDef([
                'capacityCcmTranslation' => $this->trans('(%s sm³)', [], 'Modules.Itptecdoc.Shop'),
                'capacityLiterTranslation' => $this->trans('%s l', [], 'Modules.Itptecdoc.Shop'),
                'cylindersTranslation' => $this->trans('%s cylinders', [], 'Modules.Itptecdoc.Shop'),
                'powerTranslation' => $this->trans('%s kw / %s hp', [], 'Modules.Itptecdoc.Shop'),
            ]);

            $this->context->smarty->assign([
                'article' => $this->article,
                'linked_manufacturers' => $linkedManufacturers,
                'show_manufacturers_logo' => Configuration::get(ConfigurationConstant::TECDOC_SHOW_MANUFACTURERS_LOGO),
            ]);

            $this->setTemplate('module:' . $this->module->name . '/views/templates/front/article.tpl');
        }
    }

    /**
     * @return bool
     */
    public function setMedia(): bool
    {
        $this->registerJavascript('module-' . $this->module->name . '-fslightbox', 'modules/' . $this->module->name . '/views/js/plugins/fslightbox.js', ['position' => 'bottom', 'priority' => 80]);
        $this->registerJavascript('module-' . $this->module->name . '-sprintf', 'modules/' . $this->module->name . '/views/js/plugins/sprintf.min.js', ['position' => 'bottom', 'priority' => 80]);
        $this->registerJavascript('module-' . $this->module->name . '-swiper', 'modules/' . $this->module->name . '/views/js/plugins/swiper-bundle.min.js', ['position' => 'bottom', 'priority' => 80]);
        $this->registerStylesheet('module-' . $this->module->name . '-swiper', 'modules/' . $this->module->name . '/views/css/plugins/swiper-bundle.min.css');
        $this->registerJavascript('module-' . $this->module->name . '-compatibles', 'modules/' . $this->module->name . '/views/js/front/compatibles.js', ['position' => 'bottom', 'priority' => 80]);
        $this->registerJavascript('module-' . $this->module->name . '-thumbnails', 'modules/' . $this->module->name . '/views/js/front/thumbnails.js', ['position' => 'bottom', 'priority' => 80]);

        return parent::setMedia();
    }

    /**
     * @throws GuzzleException
     * @throws MappingError
     * @throws PrestaShopException
     * @throws SmartyException
     * @throws TecDocApiException
     * @throws Symfony\Component\Cache\Exception\CacheException
     */
    public function displayAjaxGetLinkedVehicles(): void
    {
        header('Content-Type: application/json');

        if ($manufacturerId = (int) Tools::getValue('manufacturer_id')) {
            $groupedVehicles = $this
                ->tecdoc
                ->articles()
                ->getArticleLinkedVehicles($this->article->getId(), $manufacturerId, true);

            $this->ajaxRender(
                Helper::serializeObjectToJson(['grouped_vehicles' => $groupedVehicles])
            );
        }
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
                'title' => $this->article->getName(),
                'url' => $this->article->getLink(),
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
            $title = Configuration::get(ConfigurationConstant::TECDOC_META_TITLE_FOR_ARTICLE, $this->context->language->id)
                ?: '%name% %brand% %reference%';

            $description = Configuration::get(ConfigurationConstant::TECDOC_META_DESCRIPTION_FOR_ARTICLE, $this->context->language->id);

            $page['body_classes']['tecdoc-article-page'] = true;
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

        return $this->article->getLink();
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function prepareMetaData(string $string): string
    {
        $search = [
            '%name%',
            '%brand%',
            '%reference%',
        ];

        $replace = [
            $this->article->getName(false),
            $this->article->brandName,
            $this->article->reference,
        ];

        return str_replace($search, $replace, $string);
    }
}
