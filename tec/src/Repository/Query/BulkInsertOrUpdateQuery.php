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

namespace ItPremium\TecDoc\Repository\Query;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Schema\Identifier;
use Doctrine\DBAL\Types\Types;
use ItPremium\TecDoc\Model\Import\Interface\ImportEntityInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class BulkInsertOrUpdateQuery
{
    /**
     * BulkInsertOrUpdateQuery constructor.
     *
     * @param Connection $connection
     */
    public function __construct(
        private readonly Connection $connection,
    ) {
    }

    /**
     * @param string $table
     * @param ImportEntityInterface[] $dataset
     * @param string[] $updateFields
     *
     * @return int
     *
     * @throws Exception
     */
    public function execute(string $table, array $dataset, array $updateFields = []): int
    {
        if (empty($dataset)) {
            return 0;
        }

        $dataset = array_map(function (ImportEntityInterface $importEntity) {
            return $importEntity->toArray();
        }, $dataset);

        $sql = $this->generateSql($this->connection->getDatabasePlatform(), new Identifier($table), $dataset, $updateFields);

        return $this->connection->executeStatement($sql, $this->getParameters($dataset), $this->getTypes($dataset));
    }

    /**
     * @param AbstractPlatform $platform
     * @param Identifier $table
     * @param array $dataset
     * @param array $updateFields
     *
     * @return string
     */
    private function generateSql(AbstractPlatform $platform, Identifier $table, array $dataset, array $updateFields = []): string
    {
        $columns = $this->quoteColumns($platform, $this->extractColumns($dataset));

        return sprintf(
            'INSERT INTO %s %s VALUES %s ON DUPLICATE KEY UPDATE %s;',
            $table->getQuotedName($platform),
            $this->stringifyColumns($columns),
            $this->generatePlaceholders(count($columns), count($dataset)),
            $this->generateUpdateFields($updateFields),
        );
    }

    /**
     * @param AbstractPlatform $platform
     * @param array $columns
     *
     * @return array
     */
    private function quoteColumns(AbstractPlatform $platform, array $columns): array
    {
        $mapper = static fn (string $column) => (new Identifier($column))->getQuotedName($platform);

        return array_map($mapper, $columns);
    }

    /**
     * @param array $dataset
     *
     * @return array
     */
    private function extractColumns(array $dataset): array
    {
        if (empty($dataset)) {
            return [];
        }

        $first = reset($dataset);

        return array_keys($first);
    }

    /**
     * @param array $columns
     *
     * @return string
     */
    private function stringifyColumns(array $columns): string
    {
        return empty($columns) ? '' : sprintf('(%s)', implode(', ', $columns));
    }

    /**
     * @param int $columnsLength
     * @param int $datasetLength
     *
     * @return string
     */
    private function generatePlaceholders(int $columnsLength, int $datasetLength): string
    {
        $placeholders = sprintf('(%s)', implode(', ', array_fill(0, $columnsLength, '?')));

        return implode(', ', array_fill(0, $datasetLength, $placeholders));
    }

    /**
     * @param array $updateFields
     *
     * @return string
     */
    private function generateUpdateFields(array $updateFields = []): string
    {
        $updateFieldsQuery = [];

        foreach ($updateFields as $updateField) {
            $updateFieldsQuery[] = sprintf('%s = VALUES(%s)', $updateField, $updateField);
        }

        return implode(', ', $updateFieldsQuery);
    }

    /**
     * @param array $dataset
     *
     * @return array
     */
    private function getParameters(array $dataset): array
    {
        $reducer = static fn (array $flattenedValues, array $dataset) => array_merge($flattenedValues, array_values($dataset));

        return array_reduce($dataset, $reducer, []);
    }

    /**
     * @param array $dataset
     *
     * @return array
     */
    private function getTypes(array $dataset): array
    {
        if (empty($dataset)) {
            return [];
        }

        $types = [];

        foreach ($dataset[0] as $value) {
            $types[] = match (true) {
                is_int($value) => Types::INTEGER,
                is_float($value) => Types::FLOAT,
                is_bool($value) => Types::BOOLEAN,
                default => Types::STRING,
            };
        }

        $positionalTypes = [];

        for ($idx = 1; $idx <= count($dataset); ++$idx) {
            $positionalTypes = array_merge($positionalTypes, $types);
        }

        return $positionalTypes;
    }
}
