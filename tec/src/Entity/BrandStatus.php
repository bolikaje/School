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

class BrandStatus extends \ObjectModel
{
    /** @var int */
    public $id;

    /** @var int */
    public $id_tecdoc_brand;

    /** @var int */
    public $quality;

    /** @var bool */
    public $active;

    /** @var array */
    public static $definition = [
        'table' => DatabaseConstant::TECDOC_BRAND_STATUS_TABLE,
        'primary' => 'id_tecdoc_brand_status',
        'fields' => [
            'id_tecdoc_brand' => ['type' => self::TYPE_INT, 'required' => true, 'validate' => 'isInt', 'size' => 11],
            'quality' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 1],
            'active' => ['type' => self::TYPE_BOOL],
        ],
    ];

    /**
     * @param int $tecdocBrandId
     * @param int $quality
     * @param bool $active
     *
     * @return bool
     */
    public static function createOrUpdateByBrandId(int $tecdocBrandId, int $quality, bool $active = true): bool
    {
        return \Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_BRAND_STATUS_TABLE . ' (`id_tecdoc_brand`,`quality`, `active`)
                    VALUES (' . $tecdocBrandId . ', ' . $quality . ', ' . (int) $active . ')
                    ON DUPLICATE KEY UPDATE quality = VALUES(quality), active = VALUES(active);'
        );
    }

    /**
     * @param int $tecdocBrandId
     *
     * @return mixed
     */
    public static function getQuality(int $tecdocBrandId): mixed
    {
        return \Db::getInstance()->getValue(
            'SELECT quality FROM ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_BRAND_STATUS_TABLE . ' WHERE id_tecdoc_brand = ' . $tecdocBrandId
        );
    }

    /**
     * @param int $tecdocBrandId
     *
     * @return bool
     */
    public static function getStatus(int $tecdocBrandId): bool
    {
        $result = \Db::getInstance()->getValue(
            'SELECT active FROM ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_BRAND_STATUS_TABLE . ' WHERE id_tecdoc_brand = ' . $tecdocBrandId
        );

        return !($result !== false) or $result;
    }

    /**
     * @param int $tecdocBrandId
     *
     * @return bool
     */
    public static function updateStatus(int $tecdocBrandId): bool
    {
        $active = self::getStatus($tecdocBrandId);

        return \Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_BRAND_STATUS_TABLE . ' (`id_tecdoc_brand`, `active`) VALUES (' . $tecdocBrandId . ', ' . (int) !$active . ') ON DUPLICATE KEY UPDATE active = VALUES(active);'
        );
    }
}
