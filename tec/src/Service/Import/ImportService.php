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

namespace ItPremium\TecDoc\Service\Import;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use ItPremium\TecDoc\Entity\Import;
use ItPremium\TecDoc\Enum\ImportEntity;
use ItPremium\TecDoc\Enum\ImportStatus;
use ItPremium\TecDoc\Model\Import\StockImport;
use ItPremium\TecDoc\Model\Import\SupplierImport;
use ItPremium\TecDoc\Repository\StockRepository;
use ItPremium\TecDoc\Repository\SupplierRepository;
use ItPremium\TecDoc\Utils\Helper;
use Symfony\Contracts\Translation\TranslatorInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ImportService
{
    /**
     * @var Import
     */
    private Import $import;

    /**
     * @var \SplFileObject
     */
    private \SplFileObject $importFile;

    /**
     * @var string
     */
    private string $defaultSeparator = ';';

    /**
     * @var int
     */
    private int $limit = 1000;

    /**
     * @var int
     */
    private int $totalRecordsCount = 0;

    /**
     * @var int
     */
    private int $truncatedRecordsCount = 0;

    /**
     * @var array
     */
    private array $warnings = [];

    /**
     * @var array
     */
    private array $errors = [];

    /**
     * ImportService constructor.
     *
     * @param StockRepository $stockRepository
     * @param SupplierRepository $supplierRepository
     * @param TranslatorInterface $translator
     */
    public function __construct(
        private readonly StockRepository $stockRepository,
        private readonly SupplierRepository $supplierRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * Prefer to use code bellow, but it includes empty lines as well.
     * $this->importFile->seek($this->importFile->getSize());
     * return $this->importFile->key() + 1 - $rowsToSkip;
     *
     * @param int $rowsToSkip
     *
     * @return int
     */
    public function countCsvRows(int $rowsToSkip = 0): int
    {
        return iterator_count($this->importFile) - $rowsToSkip;
    }

    /**
     * @param int $offset
     *
     * @return mixed
     */
    public function getCsvHeader(int $offset = 0): mixed
    {
        $csvRows = $this->getCsvRows($offset, 1);

        return reset($csvRows);
    }

    /**
     * @param int $offset
     * @param int $limit
     *
     * @return array
     */
    public function getCsvRows(int $offset = 0, int $limit = 200): array
    {
        $offset = Helper::preventNegativeInt($offset);
        $limit = Helper::preventNegativeInt($limit);

        $fileIterator = new \LimitIterator($this->importFile, $offset, $limit);

        $rows = [];

        foreach ($fileIterator as $row) {
            $rows[] = $row;
        }

        return $rows;
    }

    /**
     * @param Import $import
     *
     * @return void
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function importStart(Import $import): void
    {
        if ($import->truncate_records) {
            $this->truncatedRecordsCount += match (ImportEntity::tryFrom((int) $import->entity)) {
                ImportEntity::STOCK => $this->stockRepository->truncate(),
                ImportEntity::SUPPLIER => $this->supplierRepository->truncate(),
            };
        }
    }

    /**
     * @param Import $import
     * @param int $offset
     *
     * @throws Exception
     */
    public function importRecords(Import $import, int $offset): void
    {
        $this->setImport($import);

        if ($this->setImportFile($import->file, $import->separator)) {
            $mappedRows = $this->getMappedCsvRows(
                $this->getCsvRows($offset, $this->limit),
            );

            match (ImportEntity::tryFrom((int) $import->entity)) {
                ImportEntity::STOCK => $this->importStock($mappedRows),
                ImportEntity::SUPPLIER => $this->importSupplier($mappedRows),
            };
        }
    }

    /**
     * @param Import $import
     *
     * @return $this
     */
    public function setImport(Import $import): static
    {
        $this->import = $import;

        return $this;
    }

    /**
     * @param string $fileName
     * @param string $separator
     *
     * @return bool
     */
    public function setImportFile(string $fileName, string $separator = ';'): bool
    {
        $filePath = $this->getFilePath($fileName);

        if (!Helper::validateFile($filePath)) {
            return false;
        }

        $importFile = new \SplFileObject($filePath);

        $importFile->setFlags(
            \SplFileObject::READ_CSV |
            \SplFileObject::SKIP_EMPTY |
            \SplFileObject::READ_AHEAD |
            \SplFileObject::DROP_NEW_LINE
        );

        $importFile->setCsvControl($separator);

        $this->importFile = $importFile;

        return true;
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function getFilePath(string $file = ''): string
    {
        return _PS_MODULE_DIR_ . 'itp_tecdoc' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * @param array $rows
     *
     * @return array
     */
    private function getMappedCsvRows(array $rows): array
    {
        $mappedColumns = array_filter($this->import->getMappedColumns(), function ($mappedColumn) {
            return $mappedColumn !== '';
        });

        $mappedRows = [];

        foreach ($rows as $row) {
            $mappedRow = [];

            foreach ($mappedColumns as $attribute => $column) {
                if (isset($row[$column])) {
                    $mappedRow[$attribute] = $row[$column];
                }
            }

            $mappedRows[] = $mappedRow;
        }

        return $mappedRows;
    }

    /**
     * @param array $mappedRows
     *
     * @throws Exception
     */
    private function importStock(array $mappedRows): void
    {
        $defaultValues = $this->import->getDefaultValues();

        $data = [];

        foreach ($mappedRows as $mappedRow) {
            $tecdocSupplierId = $mappedRow['id_tecdoc_supplier'] ?? $defaultValues['id_tecdoc_supplier'];

            if (!$tecdocSupplierId) {
                $this->addWarning('Missing supplier');

                continue;
            }

            $brand = Helper::safeStringLength($mappedRow['brand'] ?? $defaultValues['brand']);

            if (!$brand) {
                $this->addWarning('Missing brand');

                continue;
            }

            $reference = Helper::safeStringLength($mappedRow['reference'] ?? $defaultValues['reference']);

            if (!$reference) {
                $this->addWarning('Missing reference');

                continue;
            }

            $price = Helper::extractFloat($mappedRow['price'] ?? $defaultValues['price']);

            if (!$price) {
                $this->addWarning('Missing price');

                continue;
            }

            $stock = $mappedRow['stock'] ?? $defaultValues['stock'];

            if (!$stock) {
                $this->addWarning('Missing stock');

                continue;
            }

            if ($this->import->reference_suffix and str_starts_with($reference, $this->import->reference_suffix)) {
                $reference = ltrim($reference, $this->import->reference_suffix);
            }

            if ($this->import->reference_postfix and str_ends_with($reference, $this->import->reference_postfix)) {
                $reference = rtrim($reference, $this->import->reference_postfix);
            }

            $name = Helper::safeStringLength($mappedRow['name'] ?? $defaultValues['name']);
            $wholesalePrice = Helper::extractFloat($mappedRow['wholesale_price'] ?? $defaultValues['wholesale_price']);
            $deposit = Helper::extractFloat($mappedRow['deposit'] ?? $defaultValues['deposit']);
            $minimumOrderQuantity = $mappedRow['minimum_order_quantity'] ?? $defaultValues['minimum_order_quantity'] ?? 1;
            $enforceQuantityMultiple = $mappedRow['enforce_quantity_multiple'] ?? $defaultValues['enforce_quantity_multiple'] ?? false;
            $deliveryTime = Helper::safeStringLength($mappedRow['delivery_time'] ?? $defaultValues['delivery_time']);
            $weight = Helper::extractFloat($mappedRow['weight'] ?? $defaultValues['weight']);
            $oem = $mappedRow['oem'] ?? $defaultValues['oem'];
            $active = $mappedRow['active'] ?? $defaultValues['active'];

            $data[] = new StockImport(
                tecdocSupplierId: (int) $tecdocSupplierId,
                brand: $brand,
                reference: $reference,
                price: $price,
                stock: (int) $stock,
                deliveryTime: (int) $deliveryTime,
                name: $name,
                wholesalePrice: $wholesalePrice,
                deposit: $deposit,
                weight: $weight,
                oem: (bool) $oem,
                active: (bool) $active,
                minimumOrderQuantity: (int) $minimumOrderQuantity,
                enforceQuantityMultiple: (bool) $enforceQuantityMultiple
            );

            ++$this->totalRecordsCount;
        }

        if ($data) {
            $this->stockRepository->bulkInsertAndUpdate($data);
        }
    }

    /**
     * @param array $mappedRows
     *
     * @throws Exception
     */
    private function importSupplier(array $mappedRows): void
    {
        $defaultValues = $this->import->getDefaultValues();

        $data = [];

        foreach ($mappedRows as $mappedRow) {
            $name = Helper::safeStringLength($mappedRow['name'] ?? $defaultValues['name']);

            if (!$name) {
                $this->addWarning('Missing name');

                continue;
            }

            $email = Helper::safeStringLength($mappedRow['email'] ?? $defaultValues['email']);
            $phone = Helper::safeStringLength($mappedRow['phone'] ?? $defaultValues['phone']);
            $address = Helper::safeStringLength($mappedRow['address'] ?? $defaultValues['address']);
            $active = $mappedRow['active'] ?? $defaultValues['active'];

            $data[] = new SupplierImport(
                $name,
                $email,
                $phone,
                $address,
                (bool) $active
            );

            ++$this->totalRecordsCount;
        }

        if ($data) {
            $this->supplierRepository->bulkInsertAndUpdate($data);
        }
    }

    /**
     * ToDo might rework this into repository later,
     *
     * @param Import $import
     * @param bool $error
     *
     * @return bool
     *
     * @throws \PrestaShopException
     */
    public function updateImportStatus(Import $import, bool $error = false): bool
    {
        $import->status = $error
            ? ImportStatus::FAILED->value
            : ImportStatus::IMPORTED->value;

        $import->date_import = date('Y-m-d H:i:s');

        return $import->save();
    }

    /**
     * @param array $availableMappingColumns
     * @param array $columns
     * @param array $defaultValues
     *
     * @return bool
     */
    public function validateMapping(array $availableMappingColumns, array $columns, array $defaultValues): bool
    {
        $isValid = true;

        foreach ($availableMappingColumns as $fieldKey => $field) {
            if (!isset($columns[$fieldKey]) or !isset($defaultValues[$fieldKey])) {
                $isValid = false;
            }

            if (isset($field['required']) and $field['required'] and $isValid) {
                if ($columns[$fieldKey] == '' and $defaultValues[$fieldKey] == '') {
                    $isValid = false;
                }
            }
        }

        return $isValid;
    }

    /**
     * @return string
     */
    public function getDefaultSeparator(): string
    {
        return $this->defaultSeparator;
    }

    /**
     * @param $separator
     *
     * @return $this
     */
    public function setDefaultSeparator($separator): static
    {
        $this->defaultSeparator = $separator;

        return $this;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getTotalRecordsCount(): int
    {
        return $this->totalRecordsCount;
    }

    /**
     * @return int
     */
    public function getTruncatedRecordsCount(): int
    {
        return $this->truncatedRecordsCount;
    }

    /**
     * @return array
     */
    public function getWarnings(): array
    {
        return $this->warnings;
    }

    /**
     * @return array
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $warningText
     * @param array $parameters
     */
    public function addWarning(string $warningText, array $parameters = []): void
    {
        $this->warnings[] = $this->translator->trans($warningText, $parameters, 'Modules.Itptecdoc.Admin');
    }

    /**
     * @param string $errorText
     * @param array $parameters
     */
    public function addError(string $errorText, array $parameters = []): void
    {
        $this->errors[] = $this->translator->trans($errorText, $parameters, 'Modules.Itptecdoc.Admin');
    }
}
