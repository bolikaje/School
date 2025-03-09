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

namespace ItPremium\TecDoc\Constant;

if (!defined('_PS_VERSION_')) {
    exit;
}

class TabConstant
{
    public const TECDOC_PARENT_TAB = 'AdminTecDoc';
    public const TECDOC_STOCK_TAB = 'AdminTecDocStock';
    public const TECDOC_MARGINS_TAB = 'AdminTecDocMargins';
    public const TECDOC_DISCOUNTS_TAB = 'AdminTecDocDiscounts';
    // public const TECDOC_CROSSES_TAB = 'AdminTecDocCrosses';
    public const TECDOC_SUPPLIERS_TAB = 'AdminTecDocSuppliers';
    // public const TECDOC_ASSEMBLY_GROUPS_TAB = 'AdminTecDocAssemblyGroups';
    public const TECDOC_MANUFACTURERS_TAB = 'AdminTecDocManufacturers';
    public const TECDOC_BRANDS_TAB = 'AdminTecDocBrands';
    public const TECDOC_WIDGETS_TAB = 'AdminTecDocWidgets';
    public const TECDOC_IMPORT_TAB = 'AdminTecDocImport';
    public const TECDOC_SETTINGS_TAB = 'AdminTecDocSettings';

    /**
     * Define module tabs
     *
     * @var array
     */
    public static array $tabs = [
        self::TECDOC_PARENT_TAB => 'TecDoc module',
        self::TECDOC_STOCK_TAB => 'Stock',
        self::TECDOC_MARGINS_TAB => 'Margins',
        self::TECDOC_DISCOUNTS_TAB => 'Discounts',
        // self::TECDOC_CROSSES_TAB => 'Crosses',
        self::TECDOC_SUPPLIERS_TAB => 'Suppliers',
        // self::TECDOC_ASSEMBLY_GROUPS_TAB => 'Assembly groups',
        self::TECDOC_MANUFACTURERS_TAB => 'Manufacturers',
        self::TECDOC_BRANDS_TAB => 'Brands',
        self::TECDOC_WIDGETS_TAB => 'Widgets',
        self::TECDOC_IMPORT_TAB => 'Import master',
        self::TECDOC_SETTINGS_TAB => 'Settings',
    ];

    /**
     * Define module tab icons
     *
     * @var array
     */
    public static array $tabIcons = [
        self::TECDOC_PARENT_TAB => '',
        self::TECDOC_STOCK_TAB => 'inventory',
        self::TECDOC_MARGINS_TAB => 'add_shopping_cart',
        self::TECDOC_DISCOUNTS_TAB => 'percent',
        // self::TECDOC_CROSSES_TAB => 'swap_horiz',
        self::TECDOC_SUPPLIERS_TAB => 'warehouse',
        // self::TECDOC_ASSEMBLY_GROUPS_TAB => 'table_view',
        self::TECDOC_MANUFACTURERS_TAB => 'directions_car',
        self::TECDOC_BRANDS_TAB => 'factory',
        self::TECDOC_WIDGETS_TAB => 'widgets',
        self::TECDOC_IMPORT_TAB => 'sync',
        self::TECDOC_SETTINGS_TAB => 'settings',
    ];
}
