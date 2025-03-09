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

use ItPremium\TecDoc\Constant\DatabaseConstant;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Product extends ProductCore
{
    /** @var ?int TecDoc supplier identifier */
    public $id_tecdoc_supplier;

    /** @var ?int TecDoc article brand identifier */
    public $id_tecdoc_brand;

    /** @var ?string TecDoc article reference */
    public $article_reference;

    /** @var ?int Custom article identifier */
    public $id_custom_article;

    /** @var ?int Deposit product id */
    public $id_product_deposit;

    /** @var ?int Deposit parent product id */
    public $id_product_deposit_parent;

    /** @var bool */
    public $enforce_quantity_multiple = false;

    /**
     * Product constructor.
     *
     * @param null $productId
     * @param false $full
     * @param null $langId
     * @param null $shopId
     * @param ?Context $context
     */
    public function __construct($productId = null, $full = false, $langId = null, $shopId = null, ?Context $context = null)
    {
        parent::__construct($productId, $full, $langId, $shopId, $context);

        if ($this->id) {
            if ($tecdocProductProperties = $this->getTecdocProductProperties()) {
                $this->id_tecdoc_supplier = $tecdocProductProperties['id_tecdoc_supplier'];
                $this->id_tecdoc_brand = $tecdocProductProperties['id_tecdoc_brand'];
                $this->article_reference = $tecdocProductProperties['article_reference'];
                $this->enforce_quantity_multiple = $tecdocProductProperties['enforce_quantity_multiple'];
            } elseif ($customProductProperties = $this->getCustomProductProperties()) {
                $this->id_tecdoc_supplier = $customProductProperties['id_tecdoc_supplier'];
                $this->id_custom_article = $customProductProperties['id_custom_article'];
                $this->enforce_quantity_multiple = $customProductProperties['enforce_quantity_multiple'];
            }

            $this->id_product_deposit = $this->getDepositProductId();
            $this->id_product_deposit_parent = $this->getDepositParentProductId();
        }
    }

    /**
     * @return bool
     *
     * @throws PrestaShopException
     */
    public function delete(): bool
    {
        if (!$result = parent::delete()) {
            return false;
        }

        if ($this->isTecDocArticleProduct()) {
            $this->unmarkAsTecDocArticleProduct();
        }

        if ($this->isCustomArticleProduct()) {
            $this->unmarkAsCustomArticleProduct();
        }

        if ($this->id_product_deposit_parent) {
            $this->unmarkAsDepositProduct();
        }

        if ($depositProduct = $this->getDepositProduct()) {
            $depositProduct->delete();
        }

        return $result;
    }

    /**
     * @param int $tecdocSupplierId
     * @param int $tecdocBrandId
     * @param string $articleReference
     * @param bool $enforceQuantityMultiple
     *
     * @throws PrestaShopDatabaseException
     */
    public function markAsTecDocArticleProduct(int $tecdocSupplierId, int $tecdocBrandId, string $articleReference, bool $enforceQuantityMultiple = false): void
    {
        Db::getInstance()->insert(DatabaseConstant::TECDOC_PRODUCT_TABLE, [
            'id_product' => $this->id,
            'id_tecdoc_supplier' => $tecdocSupplierId,
            'id_tecdoc_brand' => $tecdocBrandId,
            'article_reference' => $articleReference,
            'enforce_quantity_multiple' => $enforceQuantityMultiple,
        ]);
    }

    /**
     * @return bool
     */
    public function unmarkAsTecDocArticleProduct(): bool
    {
        return Db::getInstance()->delete(
            DatabaseConstant::TECDOC_PRODUCT_TABLE,
            'id_product = ' . (int) $this->id
        );
    }

    /**
     * @param int $tecdocSupplierId
     * @param int $customArticleId
     * @param bool $enforceQuantityMultiple
     *
     * @return void
     *
     * @throws PrestaShopDatabaseException
     */
    public function markAsCustomArticleProduct(int $tecdocSupplierId, int $customArticleId, bool $enforceQuantityMultiple = false): void
    {
        Db::getInstance()->insert(DatabaseConstant::TECDOC_CUSTOM_PRODUCT, [
            'id_product' => $this->id,
            'id_tecdoc_supplier' => $tecdocSupplierId,
            'id_custom_article' => $customArticleId,
            'enforce_quantity_multiple' => $enforceQuantityMultiple,
        ]);
    }

    /**
     * @return bool
     */
    public function unmarkAsCustomArticleProduct(): bool
    {
        return Db::getInstance()->delete(
            DatabaseConstant::TECDOC_CUSTOM_PRODUCT,
            'id_product = ' . (int) $this->id
        );
    }

    /**
     * @param Product $parentProduct
     *
     * @return void
     *
     * @throws PrestaShopDatabaseException
     */
    public function markAsDepositProduct(Product $parentProduct): void
    {
        Db::getInstance()->insert(DatabaseConstant::TECDOC_PRODUCT_DEPOSIT_TABLE, [
            'id_product' => $parentProduct->id,
            'id_product_deposit' => $this->id,
        ]);
    }

    /**
     * @return bool
     */
    public function unmarkAsDepositProduct(): bool
    {
        return Db::getInstance()->delete(
            DatabaseConstant::TECDOC_PRODUCT_DEPOSIT_TABLE,
            'id_product_deposit = ' . (int) $this->id
        );
    }

    /**
     * @return array|bool
     */
    private function getTecdocProductProperties(): array|bool
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_PRODUCT_TABLE . ' WHERE id_product = ' . (int) $this->id
        );
    }

    /**
     * @return array|bool
     */
    private function getCustomProductProperties(): array|bool
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_CUSTOM_PRODUCT . ' WHERE id_product = ' . (int) $this->id
        );
    }

    /**
     * @return int|bool
     */
    private function getDepositProductId(): int|bool
    {
        return Db::getInstance()->getValue(
            'SELECT id_product_deposit FROM ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_PRODUCT_DEPOSIT_TABLE . ' WHERE id_product = ' . (int) $this->id
        );
    }

    /**
     * @return int|bool
     */
    private function getDepositParentProductId(): int|bool
    {
        return Db::getInstance()->getValue(
            'SELECT id_product FROM ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_PRODUCT_DEPOSIT_TABLE . ' WHERE id_product_deposit = ' . (int) $this->id
        );
    }

    /**
     * @return Product|bool
     */
    public function getDepositProduct(): Product|bool
    {
        if ($this->id_product_deposit) {
            return new Product($this->id_product_deposit);
        }

        return false;
    }

    /**
     * @return Product|bool
     */
    public function getDepositParentProduct(): Product|bool
    {
        if ($this->id != $this->id_product_deposit_parent) {
            return new Product($this->id_product_deposit_parent);
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isTecDocModuleProduct(): bool
    {
        return $this->isTecDocArticleProduct() or $this->isCustomArticleProduct();
    }

    /**
     * @return bool
     */
    public function isTecDocArticleProduct(): bool
    {
        return $this->id_tecdoc_supplier and $this->id_tecdoc_brand and $this->article_reference;
    }

    /**
     * @return bool
     */
    public function isCustomArticleProduct(): bool
    {
        return $this->id_tecdoc_supplier and $this->id_custom_article;
    }
}
