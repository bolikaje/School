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

class Stock extends \ObjectModel
{
    /** @var int */
    public $id;

    /** @var int */
    public $id_tecdoc_supplier;

    /** @var string */
    public $brand;

    /** @var string */
    public $reference;

    /** @var string */
    public $name;

    /** @var float */
    public $wholesale_price;

    /** @var float */
    public $price;

    /** @var float */
    public $deposit;

    /** @var int */
    public $minimum_order_quantity = 1;

    /** @var bool */
    public $enforce_quantity_multiple = false;

    /** @var int */
    public $stock;

    /** @var string */
    public $delivery_time;

    /** @var float */
    public $weight = 0;

    /** @var bool */
    public $oem;

    /** @var bool */
    public $active;

    /** @var string Object import date in mysql format Y-m-d H:i:s */
    public $date_import;

    /** @var array */
    public static $definition = [
        'table' => DatabaseConstant::TECDOC_STOCK_TABLE,
        'primary' => 'id_tecdoc_stock',
        'fields' => [
            'id_tecdoc_supplier' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11, 'required' => true],
            'brand' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => true],
            'reference' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => true],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'wholesale_price' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'size' => 11],
            'price' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'size' => 11, 'required' => true],
            'deposit' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'size' => 11],
            'minimum_order_quantity' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'size' => 11, 'required' => true],
            'enforce_quantity_multiple' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'stock' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'size' => 11, 'required' => true],
            'delivery_time' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'size' => 11, 'required' => true],
            'weight' => ['type' => self::TYPE_FLOAT, 'validate' => 'isUnsignedFloat'],
            'oem' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'date_import' => ['type' => self::TYPE_DATE, 'validate' => 'isDateFormat'],
        ],
    ];
}
