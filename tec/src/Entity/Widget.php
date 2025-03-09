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

class Widget extends \ObjectModel
{
    /** @var int */
    public $id;

    /** @var int */
    public $id_hook;

    /** @var string */
    public $name;

    /** @var string */
    public $public_name;

    /** @var int */
    public $type;

    /** @var int */
    public $orientation;

    /** @var bool */
    public $show_linkage_target_types;

    /** @var string */
    public $assembly_groups;

    /** @var string */
    public $manufacturers;

    /** @var string */
    public $brands;

    /** @var string */
    public $custom_html;

    /** @var string */
    public $custom_id;

    /** @var string */
    public $custom_css_class;

    /** @var int */
    public $position;

    /** @var bool */
    public $show_public_name;

    /** @var bool */
    public $active;

    /** @var array */
    public static $definition = [
        'table' => DatabaseConstant::TECDOC_WIDGET_TABLE,
        'primary' => 'id_tecdoc_widget',
        'multilang' => true,
        'fields' => [
            'id_hook' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11, 'required' => true],
            'name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'required' => true],
            'public_name' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'lang' => true],
            'type' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 1, 'required' => true],
            'orientation' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 1],
            'show_linkage_target_types' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'assembly_groups' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 10000],
            'manufacturers' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 10000],
            'brands' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 10000],
            'custom_html' => ['type' => self::TYPE_HTML, 'validate' => 'isString', 'size' => 100000, 'lang' => true],
            'custom_id' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'shop' => true],
            'custom_css_class' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255, 'shop' => true],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 11],
            'show_public_name' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'active' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool', 'shop' => true],
        ],
    ];

    /**
     * @param bool $auto_date
     * @param bool $null_values
     *
     * @return bool|int|string
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function add($auto_date = true, $null_values = false)
    {
        if ($this->position <= 0) {
            $this->position = Widget::getHigherPosition() + 1;
        }

        return parent::add($auto_date, $null_values);
    }

    /**
     * @return bool
     *
     * @throws \PrestaShopException
     */
    public function delete()
    {
        if (!$result = parent::delete()) {
            return false;
        }

        Widget::cleanPositions();

        return $result;
    }

    /**
     * @param $way
     * @param $position
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     */
    public function updatePosition($way, $position): bool
    {
        if (!$res = \Db::getInstance()->executeS('SELECT `id_tecdoc_widget`, `position` FROM `' . _DB_PREFIX_ . DatabaseConstant::TECDOC_WIDGET_TABLE . '` ORDER BY `position` ASC')) {
            return false;
        }

        foreach ($res as $widget) {
            if ($widget['id_tecdoc_widget'] == $this->id) {
                $movedWidget = $widget;
            }
        }

        if (!isset($movedWidget) or !isset($position)) {
            return false;
        }

        return \Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . DatabaseConstant::TECDOC_WIDGET_TABLE . '`
            SET `position`= `position` ' . ($way ? '- 1' : '+ 1') . '
            WHERE `position`
            ' . ($way
                    ? '> ' . (int) $movedWidget['position'] . ' AND `position` <= ' . (int) $position
                    : '< ' . (int) $movedWidget['position'] . ' AND `position` >= ' . (int) $position))
            and \Db::getInstance()->execute('
            UPDATE `' . _DB_PREFIX_ . DatabaseConstant::TECDOC_WIDGET_TABLE . '`
            SET `position` = ' . (int) $position . '
            WHERE `id_tecdoc_widget` = ' . (int) $movedWidget['id_tecdoc_widget']);
    }

    /**
     * @return bool
     */
    public static function cleanPositions(): bool
    {
        \Db::getInstance()->execute('SET @i = -1', false);

        return \Db::getInstance()->execute(
            'UPDATE `' . _DB_PREFIX_ . DatabaseConstant::TECDOC_WIDGET_TABLE . '` SET `position` = @i:=@i+1 ORDER BY `position` ASC'
        );
    }

    /**
     * @return float|int|string
     */
    public static function getHigherPosition()
    {
        $position = \Db::getInstance()->getValue(
            'SELECT MAX(`position`) FROM `' . _DB_PREFIX_ . DatabaseConstant::TECDOC_WIDGET_TABLE . '`;'
        );

        return (is_numeric($position)) ? $position : -1;
    }
}
