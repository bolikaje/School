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

use Doctrine\ORM\EntityRepository;
use ItPremium\TecDoc\Entity\Doctrine\TecdocWidget;

if (!defined('_PS_VERSION_')) {
    exit;
}

class WidgetRepository extends EntityRepository
{
    /**
     * @param string $hookName
     * @param ?int $langId
     * @param ?int $shopId
     *
     * @return TecdocWidget[]
     *
     * @throws \PrestaShopDatabaseException
     */
    public function getWidgetsByHook(string $hookName, ?int $langId = null, ?int $shopId = null): array
    {
        $qb = $this
            ->createQueryBuilder('tw')
            ->select('tw')
            ->addSelect('twl')
            ->addSelect('tws')
            ->leftJoin('tw.languages', 'twl')
            ->leftJoin('tw.shops', 'tws')
            ->where('tw.hookId = :id_hook')
            ->andWhere('tw.active = :active')
            ->setParameter('id_hook', \Hook::getIdByName($hookName))
            ->setParameter('active', true)
            ->setParameter('id_shop', $shopId);

        if ($langId) {
            $qb
                ->andWhere('twl.lang = :id_lang')
                ->setParameter('id_lang', $langId);
        }

        if ($shopId) {
            $qb
                ->andWhere('tws.shop = :id_shop')
                ->andWhere('tws.active = :active')
                ->setParameter('id_shop', $shopId);
        }

        return $qb
            ->orderBy('tw.position', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
