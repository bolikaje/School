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

namespace ItPremium\TecDoc\Service;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception;
use ItPremium\TecDoc\Constant\DatabaseConstant;
use ItPremium\TecDoc\Model\StatisticData;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class StatisticService
{
    /**
     * StatisticService constructor.
     *
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * @param string $dateFrom
     * @param string $dateTo
     *
     * @return StatisticData
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getStatisticData(string $dateFrom, string $dateTo): StatisticData
    {
        $orders = $this->getOrders($dateFrom, $dateTo);
        $revenueData = $this->getRevenueData($orders);

        return new StatisticData(
            count($orders),
            $revenueData['revenue_tax_excl'],
            $revenueData['revenue_tax_incl'],
        );
    }

    /**
     * @return StatisticData
     */
    public function getStatisticDataSimulation(): StatisticData
    {
        return new StatisticData(
            rand(1, 1000),
            rand(1, 1000) / 10,
            rand(1, 1000) / 10,
        );
    }

    /**
     * We're getting only paid orders
     *
     * @param string $dateFrom
     * @param string $dateTo
     *
     * @return array
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    private function getOrders(string $dateFrom, string $dateTo): array
    {
        $qb = $this->connection->createQueryBuilder();

        return $qb
            ->select('DISTINCT o.`id_order`')
            ->from($this->dbPrefix . 'orders', 'o')
            ->innerJoin('o', $this->dbPrefix . 'order_state', 'os', 'o.`current_state` = os.`id_order_state`')
            ->innerJoin('o', $this->dbPrefix . 'order_detail', 'od', 'od.`id_order` = o.`id_order`')
            ->innerJoin('od', $this->dbPrefix . DatabaseConstant::TECDOC_ORDER_DETAIL, 'tod', 'od.`id_order_detail` = tod.`id_order_detail`')
            ->where('o.`total_paid_real` > 0')
            ->andWhere('o.`invoice_date` BETWEEN :dateFrom AND :dateTo')
            ->andWhere('os.`logable` = 1')
            ->andWhere($qb->expr()->in('o.`id_shop`', ':shopIds'))
            ->setParameter('dateFrom', $dateFrom . ' 00:00:00')
            ->setParameter('dateTo', $dateTo . ' 23:59:59')
            ->setParameter('shopIds', \Shop::getContextListShopID(), Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAllAssociative();
    }

    /**
     * @param array $orders
     *
     * @return array
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    private function getRevenueData(array $orders = []): array
    {
        $revenueTaxExcl = 0;
        $revenueTaxIncl = 0;

        $qb = $this->connection->createQueryBuilder();

        $orderDetails = $qb
            ->select('od.*')
            ->from($this->dbPrefix . 'order_detail', 'od')
            ->innerJoin('od', $this->dbPrefix . DatabaseConstant::TECDOC_ORDER_DETAIL, 'tod', 'od.`id_order_detail` = tod.`id_order_detail`')
            ->andWhere($qb->expr()->in('od.`id_order`', ':orderIds'))
            ->setParameter('orderIds', array_column($orders, 'id_order'), Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchAllAssociative();

        foreach ($orderDetails as $orderDetail) {
            $revenueTaxExcl += $orderDetail['total_price_tax_excl'];
            $revenueTaxIncl += $orderDetail['total_price_tax_incl'];
        }

        return [
            'revenue_tax_excl' => $revenueTaxExcl,
            'revenue_tax_incl' => $revenueTaxIncl,
        ];
    }
}
