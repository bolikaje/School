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

namespace ItPremium\TecDoc\Install;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Constant\DatabaseConstant;
use ItPremium\TecDoc\Constant\TabConstant;
use ItPremium\TecDoc\Entity\Widget;
use ItPremium\TecDoc\Enum\WidgetType;
use ItPremium\TecDoc\Utils\Helper;
use PrestaShopBundle\Entity\Repository\TabRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Installer
{
    /**
     * Installer constructor.
     *
     * @param TabRepository $tabRepository
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        private readonly TabRepository $tabRepository,
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * @param \Module $module
     *
     * @return bool
     *
     * @throws Exception
     * @throws \Exception
     */
    public function installAll(\Module $module): bool
    {
        if (!$this->checkPhpVersion()) {
            return false;
        }

        $this->createDatabaseTables();
        $this->createTabs($module);
        $this->updateConfiguration();
        $this->generateCronPassword();
        $this->registerHooks($module);
        $this->createWidgets();

        return true;
    }

    /**
     * @return bool
     */
    private function checkPhpVersion(): bool
    {
        return phpversion() >= '8.1';
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    private function createDatabaseTables(): void
    {
        $queries = [
            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_BRAND_STATUS_TABLE . '` (
              `id_tecdoc_brand_status` int(11) NOT NULL AUTO_INCREMENT,
              `id_tecdoc_brand` int(11) NOT NULL,
              `quality` int(1) DEFAULT NULL,
              `active` int(1) DEFAULT NULL,
              PRIMARY KEY (`id_tecdoc_brand_status`),
              UNIQUE KEY `unique` (`id_tecdoc_brand`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_COUNTRY_TABLE . '` (
              `id_tecdoc_country` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `code` varchar(255) NOT NULL,
              PRIMARY KEY (`id_tecdoc_country`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_CROSS_TABLE . '` (
              `id_tecdoc_cross` int(11) NOT NULL AUTO_INCREMENT,
              `brand` varchar(255) NOT NULL,
              `reference` varchar(255) NOT NULL,
              `cross_brand` varchar(255) NOT NULL,
              `cross_reference` varchar(255) NOT NULL,
              `type` int(1) NOT NULL,
              `active` int(1) DEFAULT NULL,
              PRIMARY KEY (`id_tecdoc_cross`),
              UNIQUE KEY `unique` (`brand`,`reference`,`cross_brand`,`cross_reference`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_CUSTOM_PRODUCT . '` (
              `id_product` int(11) NOT NULL,
              `id_tecdoc_supplier` int(11) NOT NULL,
              `id_custom_article` int(11) NOT NULL,
              `enforce_quantity_multiple` int(1) NOT NULL,
               PRIMARY KEY (`id_product`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_DISCOUNT_TABLE . '` (
              `id_tecdoc_discount` int(11) NOT NULL AUTO_INCREMENT,
              `id_tecdoc_supplier` int(11) DEFAULT NULL,
              `id_group` int(11) DEFAULT 0,
              `brand` varchar(255) DEFAULT NULL,
              `discount` decimal(25,2) DEFAULT NULL,
              `price_range_start` decimal(25,2) DEFAULT NULL,
              `price_range_end` decimal(25,2) DEFAULT NULL,
              `active` int(1) DEFAULT NULL,
              PRIMARY KEY (`id_tecdoc_discount`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_IMPORT_TABLE . '` (
              `id_tecdoc_import` int(11) NOT NULL AUTO_INCREMENT,
              `entity` int(1) DEFAULT NULL,
              `method` int(1) DEFAULT NULL,
              `file` varchar(255) DEFAULT NULL,
              `file_url` varchar(255) DEFAULT NULL,
              `ftp_host` varchar(255) DEFAULT NULL,
              `ftp_port` varchar(255) DEFAULT NULL,
              `ftp_username` varchar(255) DEFAULT NULL,
              `ftp_password` varchar(255) DEFAULT NULL,
              `ftp_file` varchar(255) DEFAULT NULL,
              `xml_path` varchar(255) DEFAULT NULL,
              `xml_nodes` varchar(255) DEFAULT NULL,
              `separator` varchar(1) DEFAULT NULL,
              `reference_suffix` varchar(255) DEFAULT NULL,
              `reference_postfix` varchar(255) DEFAULT NULL,
              `truncate_records` int(1) DEFAULT NULL,
              `rows_to_skip` int(6) UNSIGNED DEFAULT 0,
              `column_mapping` text DEFAULT NULL,
              `default_values` text DEFAULT NULL,
              `status` int(1) DEFAULT NULL,
              `date_import` datetime DEFAULT NULL,
              PRIMARY KEY (`id_tecdoc_import`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_LANGUAGE_TABLE . '` (
              `id_tecdoc_language` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `code` varchar(255) NOT NULL,
              PRIMARY KEY (`id_tecdoc_language`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_MANUFACTURER_STATUS_TABLE . '` (
              `id_tecdoc_manufacturer_status` int(11) NOT NULL AUTO_INCREMENT,
              `id_tecdoc_manufacturer` int(11) NOT NULL,
              `active` int(1) DEFAULT NULL,
              PRIMARY KEY (`id_tecdoc_manufacturer_status`),
              UNIQUE KEY `unique` (`id_tecdoc_manufacturer`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_MARGIN_TABLE . '` (
              `id_tecdoc_margin` int(11) NOT NULL AUTO_INCREMENT,
              `id_tecdoc_supplier` int(11) DEFAULT NULL,
              `brand` varchar(255) DEFAULT NULL,
              `margin` decimal(25,2) DEFAULT NULL,
              `price_range_start` decimal(25,2) DEFAULT NULL,
              `price_range_end` decimal(25,2) DEFAULT NULL,
              `active` int(1) DEFAULT NULL,
              PRIMARY KEY (`id_tecdoc_margin`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_ORDER_DETAIL . '` (
              `id_order_detail` int(11) NOT NULL,
              `tecdoc_supplier_name` varchar(255) NOT NULL,
               PRIMARY KEY (`id_order_detail`, `tecdoc_supplier_name`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_PRODUCT_TABLE . '` (
              `id_product` int(11) NOT NULL,
              `id_tecdoc_supplier` int(11) NOT NULL,
              `id_tecdoc_brand` int(11) NOT NULL,
              `article_reference` varchar(255) NOT NULL,
              `enforce_quantity_multiple` int(1) NOT NULL,
               PRIMARY KEY (`id_product`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_PRODUCT_DEPOSIT_TABLE . '` (
              `id_product` int(11) NOT NULL,
              `id_product_deposit` int(11) NOT NULL,
               PRIMARY KEY (`id_product`, `id_product_deposit`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_STOCK_TABLE . '` (
              `id_tecdoc_stock` int(11) NOT NULL AUTO_INCREMENT,
              `id_tecdoc_supplier` int(11) DEFAULT NULL,
              `brand` varchar(255) DEFAULT NULL,
              `reference` varchar(255) DEFAULT NULL,
              `name` varchar(255) DEFAULT NULL,
              `wholesale_price` decimal(25,2) DEFAULT NULL,
              `price` decimal(25,2) DEFAULT NULL,
              `deposit` decimal(25,2) DEFAULT NULL,
              `minimum_order_quantity` int(11) UNSIGNED NOT NULL DEFAULT 1,
              `enforce_quantity_multiple` int(1) NOT NULL DEFAULT(0),
              `stock` int(11) DEFAULT NULL,
              `delivery_time` int(11) UNSIGNED DEFAULT NULL,
              `weight` decimal(20,6) DEFAULT NULL,
              `oem` int(1) DEFAULT NULL,
              `active` int(1) DEFAULT NULL,
              `date_import` datetime DEFAULT NULL,
              PRIMARY KEY (`id_tecdoc_stock`),
              UNIQUE KEY `supplier_brand_reference_unique` (`id_tecdoc_supplier`,`brand`,`reference`),
              INDEX `brand_idx` (`brand`),
              INDEX `reference_idx` (`reference`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_SUPPLIER_TABLE . '` (
              `id_tecdoc_supplier` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) DEFAULT NULL,
              `email` varchar(255) DEFAULT NULL,
              `phone` varchar(255) DEFAULT NULL,
              `address` varchar(255) DEFAULT NULL,
              `active` int(1) DEFAULT NULL,
              PRIMARY KEY (`id_tecdoc_supplier`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_WIDGET_TABLE . '` (
              `id_tecdoc_widget` int(11) NOT NULL AUTO_INCREMENT,
              `id_shop` int(11) NOT NULL,
              `id_hook` int(11) NOT NULL,
              `name` varchar(255) NOT NULL,
              `type` int(1) NOT NULL,
              `orientation` int(1) NOT NULL,
              `show_linkage_target_types` int(1) DEFAULT 1,
              `assembly_groups` TEXT NULL,
              `manufacturers` TEXT NULL,
              `brands` TEXT NULL,
              `custom_id` varchar(255) DEFAULT NULL,
              `custom_css_class` varchar(255) DEFAULT NULL,
              `position` int(11) DEFAULT NULL,
              `show_public_name` int(1) DEFAULT 1,
              `active` int(1) DEFAULT 1,
              PRIMARY KEY (`id_tecdoc_widget`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_WIDGET_LANG_TABLE . '` (
              `id_tecdoc_widget` int(11) NOT NULL,
              `id_lang` int(11) NOT NULL,
              `public_name` varchar(255) DEFAULT NULL,
              `custom_html` MEDIUMTEXT NULL,
              PRIMARY KEY (`id_tecdoc_widget`, `id_lang`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',

            'CREATE TABLE IF NOT EXISTS `' . $this->dbPrefix . DatabaseConstant::TECDOC_WIDGET_SHOP_TABLE . '` (
              `id_tecdoc_widget` int(11) NOT NULL,
              `id_shop` int(11) NOT NULL,
              `custom_id` varchar(255) DEFAULT NULL,
              `custom_css_class` varchar(255) DEFAULT NULL,
              `active` int(1) DEFAULT 1,
              PRIMARY KEY (`id_tecdoc_widget`, `id_shop`)
            ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;',
        ];

        foreach ($queries as $query) {
            $this->connection->executeQuery($query);
        }
    }

    /**
     * @param \Module $module
     *
     * @return void
     */
    private function createTabs(\Module $module): void
    {
        if (!$this->tabRepository->findOneByClassName(TabConstant::TECDOC_PARENT_TAB)) {
            $parentTab = $this->createTab(TabConstant::TECDOC_PARENT_TAB, TabConstant::$tabs[TabConstant::TECDOC_PARENT_TAB], 0, $module);

            foreach (TabConstant::$tabs as $className => $name) {
                if (!$this->tabRepository->findOneByClassName($className)) {
                    $this->createTab($className, $name, (int) $parentTab->id, $module);
                }
            }
        }
    }

    /**
     * @param string $className
     * @param string $name
     * @param int $parentTabId
     * @param \Module $module
     *
     * @return \Tab
     */
    private function createTab(string $className, string $name, int $parentTabId, \Module $module): \Tab
    {
        $tab = new \Tab();
        $tab->id_parent = $parentTabId;
        $tab->module = $module->name;
        $tab->class_name = $className;
        $tab->icon = TabConstant::$tabIcons[$className];
        $tab->active = true;
        $tab->name = Helper::createMultiLangValue($name);
        $tab->wording = $name;
        $tab->wording_domain = 'Modules.Itptecdoc.Admin';
        $tab->save();

        return $tab;
    }

    /**
     * @param \Module $module
     *
     * @return void
     */
    private function registerHooks(\Module $module): void
    {
        $hooks = [
            'actionAdminProductsListingFieldsModifier',
            'actionCartUpdateQuantityBefore',
            'actionObjectOrderDetailAddAfter',
            'actionObjectProductInCartDeleteAfter',
            'actionOrdersKpiRowModifier',
            'actionOrderStatusUpdate',
            'actionProductGridQueryBuilderModifier',
            'dashboardData',
            'dashboardZoneOne',
            'displayAdminOrderProductLine',
            'displayArticleContentAfter',
            'displayBackOfficeHeader',
            'displayFooterBefore',
            'displayHeader',
            'displayLeftColumn',
            'displayTop',
            'displayVehicleSearchForm',
            'displayVehicleSearchLink',
            'gSitemapAppendUrls',
            'moduleRoutes',
        ];

        $module->registerHook($hooks);
    }

    /**
     * @return void
     */
    private function updateConfiguration(): void
    {
        foreach (ConfigurationConstant::$configurations as $key => $value) {
            if (in_array($key, ConfigurationConstant::$multilingualConfigurations)) {
                $value = Helper::createMultiLangValue($value);
            }

            \Configuration::updateValue($key, $value);
        }
    }

    /**
     * @return void
     *
     * @throws \Exception
     */
    private function generateCronPassword(): void
    {
        $password = sha1(
            uniqid()
        );

        \Configuration::updateValue(ConfigurationConstant::TECDOC_CRON_PASSWORD, $password);
    }

    /**
     * @return void
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function createWidgets(): void
    {
        $reassuranceHtml = '<div class="tecdoc-reassurance">
            <div class="tecdoc-reassurance__item">
                <svg class="tecdoc-reassurance__icon" width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7.5 18C8.32843 18 9 18.6716 9 19.5C9 20.3284 8.32843 21 7.5 21C6.67157 21 6 20.3284 6 19.5C6 18.6716 6.67157 18 7.5 18Z" stroke="#333" stroke-width="1.5"/>
                    <path d="M16.5 18.0001C17.3284 18.0001 18 18.6716 18 19.5001C18 20.3285 17.3284 21.0001 16.5 21.0001C15.6716 21.0001 15 20.3285 15 19.5001C15 18.6716 15.6716 18.0001 16.5 18.0001Z" stroke="#333" stroke-width="1.5"/>
                    <path d="M13 13V11M13 11V9M13 11H15M13 11H11" stroke="#333" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M2 3L2.26121 3.09184C3.5628 3.54945 4.2136 3.77826 4.58584 4.32298C4.95808 4.86771 4.95808 5.59126 4.95808 7.03836V9.76C4.95808 12.7016 5.02132 13.6723 5.88772 14.5862C6.75412 15.5 8.14857 15.5 10.9375 15.5H12M16.2404 15.5C17.8014 15.5 18.5819 15.5 19.1336 15.0504C19.6853 14.6008 19.8429 13.8364 20.158 12.3075L20.6578 9.88275C21.0049 8.14369 21.1784 7.27417 20.7345 6.69708C20.2906 6.12 18.7738 6.12 17.0888 6.12H11.0235M4.95808 6.12H7" stroke="#333" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
        
                <div class="tecdoc-reassurance__content">Regular discounts and best prices</div>
            </div>
        
            <div class="tecdoc-reassurance__item">
                <svg class="tecdoc-reassurance__icon" width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M21.9844 10C21.9473 8.68893 21.8226 7.85305 21.4026 7.13974C20.8052 6.12523 19.7294 5.56066 17.5777 4.43152L15.5777 3.38197C13.8221 2.46066 12.9443 2 12 2C11.0557 2 10.1779 2.46066 8.42229 3.38197L6.42229 4.43152C4.27063 5.56066 3.19479 6.12523 2.5974 7.13974C2 8.15425 2 9.41667 2 11.9415V12.0585C2 14.5833 2 15.8458 2.5974 16.8603C3.19479 17.8748 4.27063 18.4393 6.42229 19.5685L8.42229 20.618C10.1779 21.5393 11.0557 22 12 22C12.9443 22 13.8221 21.5393 15.5777 20.618L17.5777 19.5685C19.7294 18.4393 20.8052 17.8748 21.4026 16.8603C21.8226 16.1469 21.9473 15.3111 21.9844 14" stroke="#333" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M21 7.5L17 9.5M12 12L3 7.5M12 12V21.5M12 12C12 12 14.7426 10.6287 16.5 9.75C16.6953 9.65237 17 9.5 17 9.5M17 9.5V13M17 9.5L7.5 4.5" stroke="#333" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
        
                <div class="tecdoc-reassurance__content">Operational delivery across Europe</div>
            </div>
        
            <div class="tecdoc-reassurance__item">
                <svg class="tecdoc-reassurance__icon" width="24px" height="24px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 12L12 12M12 12L9 12M12 12L12 9M12 12L12 15" stroke="#333" stroke-width="1.5" stroke-linecap="round"/>
                    <path d="M3 10.4167C3 7.21907 3 5.62028 3.37752 5.08241C3.75503 4.54454 5.25832 4.02996 8.26491 3.00079L8.83772 2.80472C10.405 2.26824 11.1886 2 12 2C12.8114 2 13.595 2.26824 15.1623 2.80472L15.7351 3.00079C18.7417 4.02996 20.245 4.54454 20.6225 5.08241C21 5.62028 21 7.21907 21 10.4167C21 10.8996 21 11.4234 21 11.9914C21 14.4963 20.1632 16.4284 19 17.9041M3.19284 14C4.05026 18.2984 7.57641 20.5129 9.89856 21.5273C10.62 21.8424 10.9807 22 12 22C13.0193 22 13.38 21.8424 14.1014 21.5273C14.6796 21.2747 15.3324 20.9478 16 20.5328" stroke="#1C274C" stroke-width="1.5" stroke-linecap="round"/>
                </svg>

                <div class="tecdoc-reassurance__content">14-days money back guarantee</div>
            </div>
        </div>';

        $reassuranceWidget = new Widget();
        $reassuranceWidget->id_hook = \Hook::getIdByName('displayArticleContentAfter');
        $reassuranceWidget->name = 'Article reassurance';
        $reassuranceWidget->type = WidgetType::CUSTOM_HTML->value;
        $reassuranceWidget->custom_html = Helper::createMultiLangValue($reassuranceHtml);
        $reassuranceWidget->active = true;
        $reassuranceWidget->save();

        $searchWidget = new Widget();
        $searchWidget->id_hook = \Hook::getIdByName('displayTop');
        $searchWidget->name = 'Search';
        $searchWidget->type = WidgetType::SEARCH_FORM->value;
        $searchWidget->custom_css_class = 'order-2 ms-auto col-auto d-none d-md-flex justify-content-center';
        $searchWidget->active = true;
        $searchWidget->save();

        $tecDocInsideWidget = new Widget();
        $tecDocInsideWidget->id_hook = \Hook::getIdByName('displayFooterBefore');
        $tecDocInsideWidget->name = 'TecDoc Inside';
        $tecDocInsideWidget->type = WidgetType::TECDOC_INSIDE->value;
        $tecDocInsideWidget->custom_css_class = 'container';
        $tecDocInsideWidget->active = true;
        $tecDocInsideWidget->save();
    }
}
