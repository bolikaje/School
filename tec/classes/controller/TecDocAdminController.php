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

use ItPremium\TecDoc\TecDoc;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class TecDocAdminController extends ModuleAdminController
{
    protected TecDoc $tecdoc;

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initProcess(): void
    {
        $this->tecdoc = $this->get('it_premium.tecdoc');

        parent::initProcess();
    }

    /**
     * @param array $records
     *
     * @return array
     *
     * @throws PrestaShopException
     */
    protected function paginate(array $records): array
    {
        $pagination = $this->checkSqlLimit(0);

        if (count($records) > $pagination) {
            $records = array_slice($records, $pagination * ($this->getPage() - 1), $pagination);
        }

        return $records;
    }

    /**
     * @return int
     */
    protected function getPage(): int
    {
        return (int) Tools::getValue('submitFilter' . $this->table) ?: 1;
    }

    /**
     * @param int $conf
     *
     * @return void
     */
    protected function setTecDocAdminRedirectAfter(int $conf): void
    {
        $page = (int) Tools::getValue('page');
        $page = $page > 1 ? '&submitFilter' . $this->table . '=' . (int) $page : '';

        $this->redirect_after = self::$currentIndex . '&conf=' . $conf . '&token=' . $this->token . $page;
    }
}
