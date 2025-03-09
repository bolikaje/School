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

class ConfigurationConstant
{
    public const TECDOC_ALLOW_AVAILABILITY_REQUESTS = 'ITP_TECDOC_ALLOW_AVAILABILITY_REQUESTS';
    public const TECDOC_API_KEY = 'ITP_TECDOC_API_KEY';
    public const TECDOC_ARTICLES_PER_PAGE = 'ITP_TECDOC_ARTICLES_PER_PAGE';
    public const TECDOC_CACHE_API_RESPONSES = 'ITP_TECDOC_CACHE_API_RESPONSES';
    public const TECDOC_COUNTRY_CODE = 'ITP_TECDOC_COUNTRY_CODE';
    public const TECDOC_CRITERIA_FOR_FACETS = 'TECDOC_CRITERIA_FOR_FACETS';
    public const TECDOC_CRON_PASSWORD = 'ITP_TECDOC_CRON_PASSWORD';
    public const TECDOC_DEFAULT_LANGUAGE_CODE = 'ITP_TECDOC_DEFAULT_LANGUAGE_CODE';
    public const TECDOC_DEPOSIT_PRODUCT_NAME = 'ITP_TECDOC_DEPOSIT_PRODUCT_NAME';
    public const TECDOC_EMAIL_FOR_AVAILABILITY_REQUESTS = 'ITP_TECDOC_EMAIL_FOR_AVAILABILITY_REQUESTS';
    public const TECDOC_GENERATE_SITEMAP = 'ITP_TECDOC_GENERATE_SITEMAP';
    public const TECDOC_GROUP_MODEL_SERIES = 'ITP_TECDOC_GROUP_MODEL_SERIES';
    public const TECDOC_ID_CATEGORY = 'ITP_TECDOC_ID_CATEGORY';
    public const TECDOC_ID_TAX_RULES_GROUP = 'ITP_TECDOC_TAX_RULES_GROUP';
    public const TECDOC_INCLUDE_CUSTOMER_GROUPS_DISCOUNT = 'ITP_TECDOC_INCLUDE_CUSTOMER_GROUPS_DISCOUNT';
    public const TECDOC_META_DESCRIPTION_FOR_ARTICLE = 'ITP_TECDOC_META_DESCRIPTION_FOR_ARTICLE';
    public const TECDOC_META_DESCRIPTION_FOR_ARTICLES = 'ITP_TECDOC_META_DESCRIPTION_FOR_ARTICLES';
    public const TECDOC_META_DESCRIPTION_FOR_ASSEMBLY_GROUP = 'ITP_TECDOC_META_DESCRIPTION_FOR_ASSEMBLY_GROUP';
    public const TECDOC_META_DESCRIPTION_FOR_ASSEMBLY_GROUPS = 'ITP_TECDOC_META_DESCRIPTION_FOR_ASSEMBLY_GROUPS';
    public const TECDOC_META_DESCRIPTION_FOR_CUSTOM_ARTICLE = 'ITP_TECDOC_META_DESCRIPTION_FOR_CUSTOM_ARTICLE';
    public const TECDOC_META_DESCRIPTION_FOR_GENERIC_ARTICLE = 'ITP_TECDOC_META_DESCRIPTION_FOR_GENERIC_ARTICLE';
    public const TECDOC_META_DESCRIPTION_FOR_MANUFACTURERS = 'ITP_TECDOC_META_DESCRIPTION_FOR_MANUFACTURERS';
    public const TECDOC_META_DESCRIPTION_FOR_MODEL_SERIES = 'ITP_TECDOC_META_DESCRIPTION_FOR_MODEL_SERIES';
    public const TECDOC_META_DESCRIPTION_FOR_VEHICLES = 'ITP_TECDOC_META_DESCRIPTION_FOR_VEHICLES';
    public const TECDOC_META_TITLE_FOR_ARTICLE = 'ITP_TECDOC_META_TITLE_FOR_ARTICLE';
    public const TECDOC_META_TITLE_FOR_ARTICLES = 'ITP_TECDOC_META_TITLE_FOR_ARTICLES';
    public const TECDOC_META_TITLE_FOR_ASSEMBLY_GROUP = 'ITP_TECDOC_META_TITLE_FOR_ASSEMBLY_GROUP';
    public const TECDOC_META_TITLE_FOR_ASSEMBLY_GROUPS = 'ITP_TECDOC_META_TITLE_FOR_ASSEMBLY_GROUPS';
    public const TECDOC_META_TITLE_FOR_CUSTOM_ARTICLE = 'ITP_TECDOC_META_TITLE_FOR_CUSTOM_ARTICLE';
    public const TECDOC_META_TITLE_FOR_GENERIC_ARTICLE = 'ITP_TECDOC_META_TITLE_FOR_GENERIC_ARTICLE';
    public const TECDOC_META_TITLE_FOR_MANUFACTURERS = 'ITP_TECDOC_META_TITLE_FOR_MANUFACTURERS';
    public const TECDOC_META_TITLE_FOR_MODEL_SERIES = 'ITP_TECDOC_META_TITLE_FOR_MODEL_SERIES';
    public const TECDOC_META_TITLE_FOR_VEHICLES = 'ITP_TECDOC_META_TITLE_FOR_VEHICLES';
    public const TECDOC_MIN_MODEL_YEAR = 'ITP_TECDOC_MIN_MODEL_YEAR';
    public const TECDOC_MODULE_URL = 'ITP_TECDOC_MODULE_URL';
    public const TECDOC_PROVIDER_ID = 'ITP_TECDOC_PROVIDER_ID';
    public const TECDOC_RECAPTCHA_ENABLE = 'ITP_TECDOC_RECAPTCHA_ENABLE';
    public const TECDOC_RECAPTCHA_SECRET_KEY = 'ITP_TECDOC_RECAPTCHA_SECRET_KEY';
    public const TECDOC_RECAPTCHA_SITE_KEY = 'ITP_TECDOC_RECAPTCHA_SITE_KEY';
    public const TECDOC_ROUTE_FOR_ARTICLE = 'ITP_TECDOC_ROUTE_FOR_ARTICLE';
    public const TECDOC_ROUTE_FOR_ARTICLES = 'ITP_TECDOC_ROUTE_FOR_ARTICLES';
    public const TECDOC_ROUTE_FOR_ASSEMBLY_GROUP = 'ITP_TECDOC_ROUTE_FOR_ASSEMBLY_GROUP';
    public const TECDOC_ROUTE_FOR_ASSEMBLY_GROUPS = 'ITP_TECDOC_ROUTE_FOR_ASSEMBLY_GROUPS';
    public const TECDOC_ROUTE_FOR_CUSTOM_ARTICLE = 'ITP_TECDOC_ROUTE_FOR_CUSTOM_ARTICLE';
    public const TECDOC_ROUTE_FOR_GENERIC_ARTICLE = 'ITP_TECDOC_ROUTE_FOR_GENERIC_ARTICLE';
    public const TECDOC_ROUTE_FOR_MANUFACTURERS = 'ITP_TECDOC_ROUTE_FOR_MANUFACTURERS';
    public const TECDOC_ROUTE_FOR_MODEL_SERIES = 'ITP_TECDOC_ROUTE_FOR_MODEL_SERIES';
    public const TECDOC_ROUTE_FOR_VEHICLES = 'ITP_TECDOC_ROUTE_FOR_VEHICLES';
    public const TECDOC_SEARCH_NUMBER_TYPE = 'ITP_TECDOC_SEARCH_NUMBER_TYPE';
    public const TECDOC_SHOW_ARTICLES_WITHOUT_AVAILABILITY = 'ITP_TECDOC_SHOW_ARTICLES_WITHOUT_AVAILABILITY';
    public const TECDOC_SHOW_COMMERCIAL_VEHICLES = 'ITP_TECDOC_SHOW_COMMERCIAL_VEHICLES';
    public const TECDOC_SHOW_CUSTOM_ARTICLES = 'ITP_TECDOC_SHOW_CUSTOM_ARTICLES';
    public const TECDOC_SHOW_FACETS = 'ITP_TECDOC_SHOW_FACETS';
    public const TECDOC_SHOW_FACETS_COUNT = 'ITP_TECDOC_SHOW_FACETS_COUNT';
    public const TECDOC_SHOW_FACETS_FOR_BRANDS = 'ITP_TECDOC_SHOW_FACETS_FOR_BRANDS';
    public const TECDOC_SHOW_FACETS_FOR_CRITERIA = 'ITP_TECDOC_SHOW_FACETS_FOR_CRITERIA';
    public const TECDOC_SHOW_FACETS_FOR_DELIVERY = 'ITP_TECDOC_SHOW_FACETS_FOR_DELIVERY';
    public const TECDOC_SHOW_FACETS_FOR_GROUPS = 'ITP_TECDOC_SHOW_FACETS_FOR_GROUPS';
    public const TECDOC_SHOW_FACETS_FOR_IN_STOCK = 'ITP_TECDOC_SHOW_FACETS_FOR_IN_STOCK';
    public const TECDOC_SHOW_FACETS_FOR_QUALITY = 'ITP_TECDOC_SHOW_FACETS_FOR_QUALITY';
    public const TECDOC_SHOW_GENERIC_ARTICLE_PAGE = 'ITP_TECDOC_SHOW_GENERIC_ARTICLE_PAGE';
    public const TECDOC_SHOW_MANUFACTURERS_ALPHABETICAL_FILTER = 'ITP_TECDOC_SHOW_MANUFACTURERS_ALPHABETICAL_FILTER';
    public const TECDOC_SHOW_MANUFACTURERS_LOGO = 'ITP_TECDOC_SHOW_MANUFACTURERS_LOGO';
    public const TECDOC_SHOW_MOTORCYCLES = 'ITP_TECDOC_SHOW_MOTORCYCLES';
    public const TECDOC_SHOW_NESTED_ASSEMBLY_GROUPS_ON_ARTICLE_LISTING = 'ITP_TECDOC_SHOW_NESTED_ASSEMBLY_GROUPS_ON_ARTICLE_LISTING';
    public const TECDOC_SHOW_PASSENGER_VEHICLES = 'ITP_TECDOC_SHOW_PASSENGER_VEHICLES';
    public const TECDOC_SYNC_STOCK_QUANTITY_WITH_ORDER_STATUSES = 'ITP_TECDOC_SYNC_STOCK_QUANTITY_WITH_ORDER_STATUSES';
    public const TECDOC_THEME = 'ITP_TECDOC_THEME';

    /**
     * Define default module configuration
     *
     * @var array
     */
    public static array $configurations = [
        self::TECDOC_ALLOW_AVAILABILITY_REQUESTS => 0,
        self::TECDOC_API_KEY => '',
        self::TECDOC_ARTICLES_PER_PAGE => 12,
        self::TECDOC_CACHE_API_RESPONSES => 0,
        self::TECDOC_COUNTRY_CODE => '',
        self::TECDOC_CRITERIA_FOR_FACETS => '',
        self::TECDOC_CRON_PASSWORD => '',
        self::TECDOC_DEFAULT_LANGUAGE_CODE => '',
        self::TECDOC_DEPOSIT_PRODUCT_NAME => 'Deposit',
        self::TECDOC_EMAIL_FOR_AVAILABILITY_REQUESTS => '',
        self::TECDOC_GENERATE_SITEMAP => 0,
        self::TECDOC_GROUP_MODEL_SERIES => 1,
        self::TECDOC_ID_CATEGORY => 2,
        self::TECDOC_ID_TAX_RULES_GROUP => 0,
        self::TECDOC_INCLUDE_CUSTOMER_GROUPS_DISCOUNT => 1,
        self::TECDOC_META_DESCRIPTION_FOR_ARTICLE => '',
        self::TECDOC_META_DESCRIPTION_FOR_ARTICLES => '',
        self::TECDOC_META_DESCRIPTION_FOR_ASSEMBLY_GROUP => '',
        self::TECDOC_META_DESCRIPTION_FOR_ASSEMBLY_GROUPS => '',
        self::TECDOC_META_DESCRIPTION_FOR_CUSTOM_ARTICLE => '',
        self::TECDOC_META_DESCRIPTION_FOR_GENERIC_ARTICLE => '',
        self::TECDOC_META_DESCRIPTION_FOR_MANUFACTURERS => '',
        self::TECDOC_META_DESCRIPTION_FOR_MODEL_SERIES => '',
        self::TECDOC_META_DESCRIPTION_FOR_VEHICLES => '',
        self::TECDOC_META_TITLE_FOR_ARTICLE => '',
        self::TECDOC_META_TITLE_FOR_ARTICLES => '',
        self::TECDOC_META_TITLE_FOR_ASSEMBLY_GROUP => '',
        self::TECDOC_META_TITLE_FOR_ASSEMBLY_GROUPS => '',
        self::TECDOC_META_TITLE_FOR_CUSTOM_ARTICLE => '',
        self::TECDOC_META_TITLE_FOR_GENERIC_ARTICLE => '',
        self::TECDOC_META_TITLE_FOR_MANUFACTURERS => '',
        self::TECDOC_META_TITLE_FOR_MODEL_SERIES => '',
        self::TECDOC_META_TITLE_FOR_VEHICLES => '',
        self::TECDOC_MIN_MODEL_YEAR => 1975,
        self::TECDOC_MODULE_URL => 'tecdoc',
        self::TECDOC_PROVIDER_ID => '',
        self::TECDOC_RECAPTCHA_ENABLE => 0,
        self::TECDOC_RECAPTCHA_SECRET_KEY => '',
        self::TECDOC_RECAPTCHA_SITE_KEY => '',
        self::TECDOC_ROUTE_FOR_ARTICLE => 'article/{brand_slug}/{reference}',
        self::TECDOC_ROUTE_FOR_ARTICLES => '{linking_target_type_slug}/{manufacturer_slug}/{model_series_slug}/{vehicle_id}-{vehicle_slug}/{assembly_group_id}-{assembly_group_slug}',
        self::TECDOC_ROUTE_FOR_ASSEMBLY_GROUP => 'category/{assembly_group_id}-{assembly_group_slug}',
        self::TECDOC_ROUTE_FOR_ASSEMBLY_GROUPS => '{linking_target_type_slug}/{manufacturer_slug}/{model_series_slug}/{vehicle_id}-{vehicle_slug}',
        self::TECDOC_ROUTE_FOR_CUSTOM_ARTICLE => 'article/{brand_slug}/{reference_slug}/{id_custom_article}{-:article_slug}',
        self::TECDOC_ROUTE_FOR_GENERIC_ARTICLE => 'ga/{generic_article_id}-{generic_article_slug}',
        self::TECDOC_ROUTE_FOR_MANUFACTURERS => '{linking_target_type_slug}',
        self::TECDOC_ROUTE_FOR_MODEL_SERIES => '{linking_target_type_slug}/{manufacturer_slug}',
        self::TECDOC_ROUTE_FOR_VEHICLES => '{linking_target_type_slug}/{manufacturer_slug}/{model_series_id}-{model_series_slug}',
        self::TECDOC_SEARCH_NUMBER_TYPE => 10,
        self::TECDOC_SHOW_ARTICLES_WITHOUT_AVAILABILITY => 1,
        self::TECDOC_SHOW_COMMERCIAL_VEHICLES => 1,
        self::TECDOC_SHOW_CUSTOM_ARTICLES => 1,
        self::TECDOC_SHOW_FACETS => 1,
        self::TECDOC_SHOW_FACETS_COUNT => 1,
        self::TECDOC_SHOW_FACETS_FOR_BRANDS => 1,
        self::TECDOC_SHOW_FACETS_FOR_CRITERIA => 1,
        self::TECDOC_SHOW_FACETS_FOR_DELIVERY => 1,
        self::TECDOC_SHOW_FACETS_FOR_GROUPS => 1,
        self::TECDOC_SHOW_FACETS_FOR_IN_STOCK => 1,
        self::TECDOC_SHOW_FACETS_FOR_QUALITY => 1,
        self::TECDOC_SHOW_GENERIC_ARTICLE_PAGE => 0,
        self::TECDOC_SHOW_MANUFACTURERS_ALPHABETICAL_FILTER => 1,
        self::TECDOC_SHOW_MANUFACTURERS_LOGO => 1,
        self::TECDOC_SHOW_MOTORCYCLES => 1,
        self::TECDOC_SHOW_NESTED_ASSEMBLY_GROUPS_ON_ARTICLE_LISTING => 0,
        self::TECDOC_SHOW_PASSENGER_VEHICLES => 1,
        self::TECDOC_SYNC_STOCK_QUANTITY_WITH_ORDER_STATUSES => 1,
        self::TECDOC_THEME => 1,
    ];

    /**
     * Define multilingual configurations
     *
     * @var array
     */
    public static array $multilingualConfigurations = [
        self::TECDOC_DEPOSIT_PRODUCT_NAME,
        self::TECDOC_META_DESCRIPTION_FOR_ARTICLE,
        self::TECDOC_META_DESCRIPTION_FOR_ARTICLES,
        self::TECDOC_META_DESCRIPTION_FOR_ASSEMBLY_GROUP,
        self::TECDOC_META_DESCRIPTION_FOR_ASSEMBLY_GROUPS,
        self::TECDOC_META_DESCRIPTION_FOR_CUSTOM_ARTICLE,
        self::TECDOC_META_DESCRIPTION_FOR_GENERIC_ARTICLE,
        self::TECDOC_META_DESCRIPTION_FOR_MANUFACTURERS,
        self::TECDOC_META_DESCRIPTION_FOR_MODEL_SERIES,
        self::TECDOC_META_DESCRIPTION_FOR_VEHICLES,
        self::TECDOC_META_TITLE_FOR_ARTICLE,
        self::TECDOC_META_TITLE_FOR_ARTICLES,
        self::TECDOC_META_TITLE_FOR_ASSEMBLY_GROUP,
        self::TECDOC_META_TITLE_FOR_ASSEMBLY_GROUPS,
        self::TECDOC_META_TITLE_FOR_CUSTOM_ARTICLE,
        self::TECDOC_META_TITLE_FOR_GENERIC_ARTICLE,
        self::TECDOC_META_TITLE_FOR_MANUFACTURERS,
        self::TECDOC_META_TITLE_FOR_MODEL_SERIES,
        self::TECDOC_META_TITLE_FOR_VEHICLES,
    ];
}
