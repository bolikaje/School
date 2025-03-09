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

namespace ItPremium\TecDoc\Extensions\SupplierModule;

use Doctrine\DBAL\Exception;
use ItPremium\TecDoc\Constant\DatabaseConstant;
use ItPremium\TecDoc\Model\Import\StockImport;
use ItPremium\TecDoc\Repository\StockRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class AbstractSupplierModuleService
{
    /**
     * @var int
     */
    protected readonly int $tecdocSupplierId;

    /**
     * @var string
     */
    private string $importDateTime;

    /**
     * @var StockImport[]
     */
    protected array $stockImports = [];

    /**
     * @param StockRepository $stockRepository
     */
    public function __construct(
        protected readonly StockRepository $stockRepository,
    ) {
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function createStockRecords(): void
    {
        if (!empty($this->stockImports)) {
            $this->flushStockRecords();
        }

        $this->deleteOutdatedStockRecords();
    }

    /**
     * @param int $iteration
     *
     * @return void
     *
     * @throws Exception
     */
    protected function flushStockRecords(int $iteration = 0): void
    {
        if ($iteration % 250 == 0) {
            $this->stockRepository->bulkInsertAndUpdate($this->stockImports);

            $this->stockImports = [];
        }
    }

    /**
     * The standard procedure for removing outdated stock information involves comparing the dates of import.
     * If necessary, you can customize this method within the Supplier module.
     *
     * @return void
     */
    protected function deleteOutdatedStockRecords(): void
    {
        if ($this->importDateTime) {
            $query = 'DELETE FROM ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_STOCK_TABLE . ' WHERE id_tecdoc_supplier = ' . $this->tecdocSupplierId . ' AND date_import < "' . $this->importDateTime . '";';
        } else {
            $query = 'DELETE FROM ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_STOCK_TABLE . ' WHERE id_tecdoc_supplier = ' . $this->tecdocSupplierId . ' AND date_import < DATE_SUB(NOW(), INTERVAL 1 DAY);';
        }

        \Db::getInstance()->execute($query);
    }

    /**
     * @param string $moduleConfigurationKey
     *
     * @return bool
     */
    protected function setImportDateTime(string $moduleConfigurationKey): bool
    {
        $this->importDateTime = date('Y-m-d H:i:s');

        return \Configuration::updateValue($moduleConfigurationKey, $this->importDateTime);
    }
}
