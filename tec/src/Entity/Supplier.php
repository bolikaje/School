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

declare(strict_types=1);

namespace ItPremium\TecDoc\Entity;

use ItPremium\TecDoc\Constant\DatabaseConstant;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Supplier extends \ObjectModel
{
    /** @var int */
    public $id;

    /** @var string */
    public $name;

    /** @var string */
    public $email;

    /** @var string */
    public $phone;

    /** @var string */
    public $address;

    /** @var bool */
    public $active;

    /** @var array */
    public static $definition = [
        'table' => DatabaseConstant::TECDOC_SUPPLIER_TABLE,
        'primary' => 'id_tecdoc_supplier',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => true],
            'email' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'phone' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'address' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
        ],
    ];

    /**
     * We need to make sure that we clear all relations too
     *
     * @return bool True if delete was successful
     *
     * @throws \PrestaShopException
     */
    public function delete()
    {
        if (!$result = parent::delete()) {
            return false;
        }

        /*
         * Delete all related margins
         */
        \Db::getInstance()->delete(
            DatabaseConstant::TECDOC_MARGIN_TABLE,
            'id_tecdoc_supplier = ' . (int) $this->id
        );

        /*
         * Delete all related discounts
         */
        \Db::getInstance()->delete(
            DatabaseConstant::TECDOC_DISCOUNT_TABLE, 'id_tecdoc_supplier = ' . (int) $this->id
        );

        /*
         * Delete all related stock
         */
        \Db::getInstance()->delete(
            DatabaseConstant::TECDOC_STOCK_TABLE, 'id_tecdoc_supplier = ' . (int) $this->id
        );

        /*
         * Delete all related cached products
         */
        $tecdocArticleProducts = \Db::getInstance()->executeS(
            'SELECT id_product FROM ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_PRODUCT_TABLE . ' WHERE id_tecdoc_supplier = ' . (int) $this->id
        );

        $customArticleProducts = \Db::getInstance()->executeS(
            'SELECT id_product FROM ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_CUSTOM_PRODUCT . ' WHERE id_tecdoc_supplier = ' . (int) $this->id
        );

        $productIds = array_merge(
            array_column($tecdocArticleProducts, 'id_product'),
            array_column($customArticleProducts, 'id_product')
        );

        foreach ($productIds as $productId) {
            $product = new \Product($productId);

            if (\Validate::isLoadedObject($product)) {
                $product->delete();
            }
        }

        return $result;
    }
}
