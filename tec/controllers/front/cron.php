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

if (!defined('_PS_VERSION_')) {
    exit;
}

class Itp_TecdocCronModuleFrontController extends TecDocFrontController
{
    /**
     * @var bool
     */
    public $auth = false;

    /** @var bool */
    public $ajax;

    /**
     * @return void
     */
    public function initContent(): void
    {
        global $kernel;

        $kernel = new AppKernel('prod', _PS_MODE_DEV_);
        $kernel->boot();
    }

    /**
     * @return void
     *
     * @throws PrestaShopException
     * @throws Doctrine\DBAL\Driver\Exception
     * @throws Doctrine\DBAL\Exception
     */
    public function display(): void
    {
        $this->ajax = 1;

        $key = (string) Tools::getValue('key');
        $action = (string) Tools::getValue('action');

        if (php_sapi_name() == 'cli' or $key == Configuration::get(ConfigurationConstant::TECDOC_CRON_PASSWORD)) {
            if ($action == 'clear-api-cache') {
                $this->module->clearApiCache();

                exit($this->trans('The API cache has been seamlessly cleared.', [], 'Modules.Itptecdoc.Shop'));
            } elseif ($action == 'delete-cached-products') {
                $this
                    ->tecdoc
                    ->products()
                    ->deleteCachedProducts();

                exit($this->trans('Cached products has been successfully deleted.', [], 'Modules.Itptecdoc.Shop'));
            } else {
                exit($this->trans('Invalid action.', [], 'Modules.Itptecdoc.Shop'));
            }
        }
    }
}
