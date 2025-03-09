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
use PrestaShop\PrestaShop\Adapter\ContainerFinder;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Link extends LinkCore
{
    /**
     * Create a link to a product.
     *
     * @param ProductCore|array|int $product Product object (can be an ID product, but deprecated)
     * @param ?string $alias
     * @param ?string $category
     * @param ?string $ean13
     * @param ?int $idLang
     * @param ?int $idShop (since 1.5.0) ID shop need to be used when we generate a product link for a product in a cart
     * @param ?int $idProductAttribute ID product attribute
     * @param bool $force_routes
     * @param bool $relativeProtocol
     * @param bool $withIdInAnchor
     * @param array $extraParams
     * @param bool $addAnchor
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    public function getProductLink(
        $product,
        $alias = null,
        $category = null,
        $ean13 = null,
        $idLang = null,
        $idShop = null,
        $idProductAttribute = null,
        $force_routes = false,
        $relativeProtocol = false,
        $withIdInAnchor = false,
        $extraParams = [],
        bool $addAnchor = true,
    ) {
        if (Validate::isLoadedObject($product) and $product->isTecDocModuleProduct()) {
            $finder = new ContainerFinder(Context::getContext());

            $productLink = $finder
                ->getContainer()
                ->get('it_premium.tecdoc')
                ->products()
                ->getProductLink($product);

            if ($productLink) {
                return $productLink;
            }
        }

        if (isset($product->id_product_deposit_parent) and $product->id_product_deposit_parent) {
            return Context::getContext()->link->getProductLink($product->id_product_deposit_parent);
        }

        return parent::getProductLink($product, $alias, $category, $ean13, $idLang, $idShop, $idProductAttribute, $force_routes, $relativeProtocol, $withIdInAnchor, $extraParams, $addAnchor);
    }

    /**
     * Create a link to a category.
     *
     * @param CategoryCore|array|int|string $category Category object (can be an ID category, but deprecated)
     * @param ?string $alias
     * @param ?int $idLang
     * @param ?string $selectedFilters Url parameter to autocheck filters of the module blocklayered
     * @param ?int $idShop
     * @param bool $relativeProtocol
     *
     * @return string
     *
     * @throws PrestaShopException
     */
    public function getCategoryLink(
        $category,
        $alias = null,
        $idLang = null,
        $selectedFilters = null,
        $idShop = null,
        $relativeProtocol = false,
    ) {
        if (Configuration::get(ConfigurationConstant::TECDOC_ID_CATEGORY) == $this->getCategoryObject($category, $idLang)->id) {
            return Context::getContext()->link->getModuleLink('itp_tecdoc', 'home', [], true);
        }

        return parent::getCategoryLink($category, $alias, $idLang, $selectedFilters, $idShop, $relativeProtocol);
    }
}
