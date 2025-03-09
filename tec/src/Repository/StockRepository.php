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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Exception;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use ItPremium\TecDoc\Constant\DatabaseConstant;
use ItPremium\TecDoc\Entity\Doctrine\TecdocStock;
use ItPremium\TecDoc\Enum\ArticleType;
use ItPremium\TecDoc\Model\Data\Article\Article;
use ItPremium\TecDoc\Model\Import\StockImport;
use ItPremium\TecDoc\Repository\Interface\ImportEntityRepositoryInterface;
use ItPremium\TecDoc\Repository\Query\BulkInsertOrUpdateQuery;
use ItPremium\TecDoc\Utils\Helper;

if (!defined('_PS_VERSION_')) {
    exit;
}

class StockRepository extends EntityRepository implements ImportEntityRepositoryInterface
{
    /**
     * @param StockImport[] $data
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
            'wholesale_price',
            'price',
            'deposit',
            'minimum_order_quantity',
            'enforce_quantity_multiple',
            'stock',
            'delivery_time',
            'weight',
            'oem',
            'active',
            'date_import',
        ];

        $bulkInsertOrUpdateQuery = new BulkInsertOrUpdateQuery($connection);

        return $bulkInsertOrUpdateQuery->execute(_DB_PREFIX_ . DatabaseConstant::TECDOC_STOCK_TABLE, $data, $updateFields);
    }

    /**
     * @return int
     *
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function truncate(): int
    {
        $qb = $this->createQueryBuilder('tsk');

        $qb->delete();

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * @param string $reference
     *
     * @return TecdocStock[]
     */
    public function findByReference(string $reference): array
    {
        $qb = $this->createQueryBuilder('tsk');

        $qb->leftJoin('tsk.tecdocSupplier', 'tsp');

        $qb
            ->where(
                // $qb->expr()->like('REPLACE(lower(tsk.reference), \' \', \'\')', ':reference')
                $qb->expr()->like('tsk.reference', ':reference')
            )
            ->andWhere('tsk.stock > 0')
            ->andWhere('tsk.stock >= tsk.minimumOrderQuantity')
            ->andWhere('tsk.price > 0')
            ->andWhere('tsk.active = 1')
            ->andWhere('tsp.active = 1')
            ->groupBy('tsk.tecdocSupplier')
            ->addGroupBy('tsk.brand')
            ->addGroupBy('tsk.reference')
            ->orderBy('tsk.price')
            ->setParameter('reference', Helper::prepareReference($reference));

        return $qb->getQuery()->getResult();
    }

    /**
     * @param Article $article
     * @param int $tecdocSupplierId
     *
     * @return ?TecdocStock
     *
     * @throws NonUniqueResultException
     */
    public function findByArticle(Article $article, int $tecdocSupplierId): ?TecdocStock
    {
        $qb = $this->createQueryBuilder('tsk');

        $qb
            ->leftJoin('tsk.tecdocSupplier', 'tsp')
            ->where('tsk.tecdocSupplier = :id_tecdoc_supplier')
            ->setParameter('id_tecdoc_supplier', $tecdocSupplierId);

        if ($article->getType() == ArticleType::TECDOC_ARTICLE) {
            $qb
                ->andWhere('tsk.brand = :brand')
                ->andWhere('tsk.reference = :reference')
                ->setParameter('brand', $article->brandName)
                ->setParameter('reference', $article->reference);
        } elseif ($article->getType() == ArticleType::CUSTOM_ARTICLE) {
            $qb
                ->andWhere('tsk.id = :id')
                ->setParameter('id', $article->getId());
        }

        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Performance is the most important factor for this method,
     * so in this case, we are not using the query builder,
     * and the result does not return mapped objects.
     *
     * @param ArrayCollection<int, Article> $articles
     *
     * @return array
     *
     * @throws \PrestaShopDatabaseException
     */
    public function findByArticles(ArrayCollection $articles): array
    {
        $sql = 'SELECT tsk.*
            FROM ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_STOCK_TABLE . ' tsk
            INNER JOIN ' . _DB_PREFIX_ . DatabaseConstant::TECDOC_SUPPLIER_TABLE . ' tsp ON tsk.id_tecdoc_supplier = tsp.id_tecdoc_supplier
            WHERE tsk.stock > 0
            AND tsk.stock >= tsk.minimum_order_quantity
            AND tsk.price > 0
            AND tsk.active = 1
            AND tsp.active = 1
            AND (tsk.brand, tsk.reference) IN (';

        $inConditions = [];

        foreach ($articles as $article) {
            $brand = mb_strtolower($article->brandName);
            $reference = Helper::prepareReference($article->reference);

            $inConditions[] = '(\'' . $brand . '\',\'' . $reference . '\')';
        }

        $sql .= implode(', ', $inConditions);
        $sql .= ') GROUP BY tsk.id_tecdoc_supplier, tsk.brand, tsk.reference ORDER BY tsk.price;';

        return \Db::getInstance()->executeS($sql) ?: [];
    }

    /**
     * @return iterable
     */
    public function getUniqueStockRecords(): iterable
    {
        $qb = $this->createQueryBuilder('tsk');

        $qb
            ->leftJoin('tsk.tecdocSupplier', 'tsp')
            ->andWhere('tsk.stock > 0')
            ->andWhere('tsk.stock >= tsk.minimumOrderQuantity')
            ->andWhere('tsk.price > 0')
            ->andWhere('tsk.active = 1')
            ->andWhere('tsp.active = 1')
            ->groupBy('tsk.brand')
            ->addGroupBy('tsk.reference');

        return $qb->getQuery()->toIterable();
    }

    /**
     * @param TecdocStock $stock
     */
    public function save(TecdocStock $stock): void
    {
        $this->_em->persist($stock);
        $this->_em->flush();
    }
}
