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

class Discount extends \ObjectModel
{
    /** @var int */
    public $id;

    /** @var int */
    public $id_tecdoc_supplier;

    /** @var int */
    public $id_group;

    /** @var string */
    public $brand;

    /** @var float */
    public $discount;

    /** @var float */
    public $price_range_start;

    /** @var float */
    public $price_range_end;

    /** @var bool */
    public $active;

    /** @var array */
    public static $definition = [
        'table' => DatabaseConstant::TECDOC_DISCOUNT_TABLE,
        'primary' => 'id_tecdoc_discount',
        'fields' => [
            'id_tecdoc_supplier' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11, 'required' => true],
            'id_group' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11],
            'brand' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'discount' => ['type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'size' => 20, 'required' => true],
            'price_range_start' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'size' => 20],
            'price_range_end' => ['type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'size' => 20],
            'active' => ['type' => self::TYPE_BOOL, 'required'],
        ],
    ];
}
