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

class DatabaseConstant
{
    /**
     *  Table to store brand rating active status
     */
    public const TECDOC_BRAND_STATUS_TABLE = 'tecdoc_brand_status';

    /**
     *  Table to store available TecDoc countries
     */
    public const TECDOC_COUNTRY_TABLE = 'tecdoc_country';

    /**
     *  Table to store article crosses
     */
    public const TECDOC_CROSS_TABLE = 'tecdoc_cross';

    /**
     *  Table to store mapping information for Custom Articles
     */
    public const TECDOC_CUSTOM_PRODUCT = 'tecdoc_custom_product';

    /**
     *  Table to store discount rules
     */
    public const TECDOC_DISCOUNT_TABLE = 'tecdoc_discount';

    /**
     *  Table to store discount rules
     */
    public const TECDOC_IMPORT_TABLE = 'tecdoc_import';

    /**
     *  Table to store available TecDoc languages
     */
    public const TECDOC_LANGUAGE_TABLE = 'tecdoc_language';

    /**
     *  Table to store manufacturer active status
     */
    public const TECDOC_MANUFACTURER_STATUS_TABLE = 'tecdoc_manufacturer_status';

    /**
     *  Table to store margin rules
     */
    public const TECDOC_MARGIN_TABLE = 'tecdoc_margin';

    /**
     *  Table to store extra information for order detail
     */
    public const TECDOC_ORDER_DETAIL = 'tecdoc_order_detail';

    /**
     *  Table to store mapping information for TecDoc articles
     */
    public const TECDOC_PRODUCT_TABLE = 'tecdoc_product';

    /**
     *  Table to store mapping information for Deposit products.
     */
    public const TECDOC_PRODUCT_DEPOSIT_TABLE = 'tecdoc_product_deposit';

    /**
     *  Table to store prices and stocks for articles and suppliers
     */
    public const TECDOC_STOCK_TABLE = 'tecdoc_stock';

    /**
     *  Table to store suppliers
     */
    public const TECDOC_SUPPLIER_TABLE = 'tecdoc_supplier';

    /**
     *  Table to store widgets
     */
    public const TECDOC_WIDGET_TABLE = 'tecdoc_widget';

    /**
     *  Table to store widgets languages information
     */
    public const TECDOC_WIDGET_LANG_TABLE = 'tecdoc_widget_lang';

    /**
     *  Table to store widgets shop associations
     */
    public const TECDOC_WIDGET_SHOP_TABLE = 'tecdoc_widget_shop';

    /**
     * Define module tables
     *
     * @var array
     */
    public static array $tables = [
        self::TECDOC_BRAND_STATUS_TABLE,
        self::TECDOC_COUNTRY_TABLE,
        self::TECDOC_CROSS_TABLE,
        self::TECDOC_CUSTOM_PRODUCT,
        self::TECDOC_DISCOUNT_TABLE,
        self::TECDOC_IMPORT_TABLE,
        self::TECDOC_LANGUAGE_TABLE,
        self::TECDOC_MANUFACTURER_STATUS_TABLE,
        self::TECDOC_MARGIN_TABLE,
        self::TECDOC_ORDER_DETAIL,
        self::TECDOC_PRODUCT_TABLE,
        self::TECDOC_PRODUCT_DEPOSIT_TABLE,
        self::TECDOC_STOCK_TABLE,
        self::TECDOC_SUPPLIER_TABLE,
        self::TECDOC_WIDGET_TABLE,
        self::TECDOC_WIDGET_LANG_TABLE,
        self::TECDOC_WIDGET_SHOP_TABLE,
    ];
}
