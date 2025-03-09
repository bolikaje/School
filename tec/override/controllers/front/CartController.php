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

use ItPremium\TecDoc\Enum\ArticleType;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CartController extends CartControllerCore
{
    /**
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        if (Tools::getIsset('article_type')) {
            $productService = $this
                ->get('it_premium.tecdoc')
                ->products();

            $articleType = ArticleType::tryFrom(Tools::getValue('article_type')) ?? ArticleType::TECDOC_ARTICLE;
            $tecdocSupplierId = (int) Tools::getValue('id_tecdoc_supplier');

            if ($articleType == ArticleType::TECDOC_ARTICLE) {
                $tecdocBrandId = (int) Tools::getValue('id_tecdoc_brand');
                $articleReference = (string) Tools::getValue('article_reference');

                if ($tecdocSupplierId and $tecdocBrandId and $articleReference) {
                    $product = $productService->getTecDocArticleAsPrestaProduct($tecdocSupplierId, $tecdocBrandId, $articleReference);
                }
            } elseif ($articleType == ArticleType::CUSTOM_ARTICLE) {
                $customArticleId = (int) Tools::getValue('id_custom_article');

                if ($tecdocSupplierId and $customArticleId) {
                    $product = $productService->getCustomArticleAsPrestaProduct($tecdocSupplierId, $customArticleId);
                }
            }

            if (isset($product) and $product instanceof Product) {
                $this->id_product = $product->id;
            }
        }
    }

    /**
     * @return void
     */
    protected function processChangeProductInCart()
    {
        $product = new Product($this->id_product);

        if ($product->isTecDocModuleProduct() and $product->enforce_quantity_multiple and $product->minimal_quantity) {
            $quantityStep = $product->minimal_quantity;

            if ($this->qty % $quantityStep !== 0) {
                $this->errors[] = $this->trans('The quantity must be a multiple of %s.', ['%step%' => $quantityStep], 'Modules.Itptecdoc.Error');

                return;
            }
        }

        parent::processChangeProductInCart();
    }
}
