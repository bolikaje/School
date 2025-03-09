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
use PrestaShopBundle\Entity\Repository\TabRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Uninstaller
{
    /**
     * Uninstaller constructor.
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
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws Exception
     */
    public function uninstallAll(): bool
    {
        $this->dropDatabaseTables();
        $this->removeTabs();
        $this->removeConfiguration();

        return true;
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    private function dropDatabaseTables(): void
    {
        foreach (DatabaseConstant::$tables as $tableName) {
            $this->connection->executeQuery('DROP TABLE IF EXISTS `' . $this->dbPrefix . $tableName . '`');
        }
    }

    /**
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function removeTabs(): void
    {
        foreach (TabConstant::$tabs as $className => $name) {
            if ($tabId = $this->tabRepository->findOneIdByClassName($className)) {
                (new \Tab($tabId))->delete();
            }
        }
    }

    /**
     * @return void
     */
    private function removeConfiguration(): void
    {
        foreach (ConfigurationConstant::$configurations as $key => $value) {
            \Configuration::deleteByName($key);
        }
    }
}
