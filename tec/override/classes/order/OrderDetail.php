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
use ItPremium\TecDoc\Constant\DatabaseConstant;
use PrestaShop\PrestaShop\Adapter\ContainerFinder;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OrderDetail extends OrderDetailCore
{
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

        Db::getInstance()->delete(
            DatabaseConstant::TECDOC_ORDER_DETAIL,
            'id_order_detail = ' . (int) $this->id
        );

        return $result;
    }

    /**
     * @param $product
     * @param $orderStateId
     *
     * @return void
     *
     * @throws PrestaShop\PrestaShop\Adapter\CoreException
     * @throws Exception
     */
    protected function updateProductQuantityInStock($product, $orderStateId): void
    {
        parent::updateProductQuantityInStock($product, $orderStateId);

        if (Configuration::get(ConfigurationConstant::TECDOC_SYNC_STOCK_QUANTITY_WITH_ORDER_STATUSES)) {
            $productObj = new Product($product['id_product']);

            if ($productObj->isTecDocModuleProduct()) {
                $finder = new ContainerFinder(Context::getContext());

                $finder->getContainer()
                    ->get('it_premium.tecdoc')
                    ->products()
                    ->updateStockQuantityByProduct($productObj, -(int) $product['cart_quantity']);
            }
        }
    }

    /**
     * @return array|bool
     */
    public function getTecDocProductInformation(): array|bool
    {
        return Db::getInstance()->getRow(
            'SELECT * FROM ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_ORDER_DETAIL . ' WHERE id_order_detail = ' . (int) $this->id
        );
    }

    /**
     * @param string $tecdocSupplierName
     *
     * @return void
     *
     * @throws PrestaShopDatabaseException
     */
    public function saveTecDocProductInformation(string $tecdocSupplierName): void
    {
        Db::getInstance()->insert(DatabaseConstant::TECDOC_ORDER_DETAIL, [
            'id_order_detail' => $this->id,
            'tecdoc_supplier_name' => $tecdocSupplierName,
        ]);
    }
}
