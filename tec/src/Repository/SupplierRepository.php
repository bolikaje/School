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

namespace ItPremium\TecDoc\Repository;

use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use ItPremium\TecDoc\Constant\DatabaseConstant;
use ItPremium\TecDoc\Model\Import\SupplierImport;
use ItPremium\TecDoc\Repository\Interface\ImportEntityRepositoryInterface;
use ItPremium\TecDoc\Repository\Query\BulkInsertOrUpdateQuery;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SupplierRepository extends EntityRepository implements ImportEntityRepositoryInterface
{
    /**
     * @param SupplierImport[] $data
     *
     * @return int
     *
     * @throws Exception
     */
    public function bulkInsertAndUpdate(array $data): int
    {
        $connection = $this->getEntityManager()->getConnection();

        $connection->getConfiguration()->setSQLLogger();

        $updateFields = [
            'name',
            'email',
            'phone',
            'address',
            'active',
        ];

        $bulkInsertOrUpdateQuery = new BulkInsertOrUpdateQuery($connection);

        return $bulkInsertOrUpdateQuery->execute(_DB_PREFIX_ . DatabaseConstant::TECDOC_SUPPLIER_TABLE, $data, $updateFields);
    }

    /**
     * @return array
     */
    public function getSuppliers(): array
    {
        return $this
            ->createQueryBuilder('ts')
            ->orderBy('ts.name')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return int
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function truncate(): int
    {
        $qb = $this->createQueryBuilder('t');

        $qb->delete();

        return $qb->getQuery()->getSingleScalarResult();
    }
}
