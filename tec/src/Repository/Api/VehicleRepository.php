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

namespace ItPremium\TecDoc\Repository\Api;

use CuyZ\Valinor\Mapper\MappingError;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Exception\GuzzleException;
use ItPremium\TecDoc\Api\Exception\TecDocApiException;
use ItPremium\TecDoc\Api\Request\GetVehicleByIdsRequest;
use ItPremium\TecDoc\Model\Data\LinkageTarget\LinkageTargetDetails;
use ItPremium\TecDoc\Model\Data\Vehicle;
use ItPremium\TecDoc\Model\Data\VehicleByKeyNumberPlates;
use ItPremium\TecDoc\Model\Query\GetLinkageTargetsQuery;
use ItPremium\TecDoc\Model\Query\GetVehiclesByKeyNumberPlatesQuery;
use Symfony\Component\Cache\Exception\CacheException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class VehicleRepository extends TecDocApiRepository
{
    /**
     * @param array $carIds
     *
     * @return ArrayCollection<int, Vehicle>
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws MappingError
     * @throws CacheException
     */
    public function getVehiclesByIds(array $carIds): ArrayCollection
    {
        $data = [];

        foreach (array_chunk($carIds, 25) as $carIdsChunk) {
            $getVehicleByIdsRequest = (new GetVehicleByIdsRequest())
                ->setCarIds($carIdsChunk);

            $response = $this
                ->tecDocApi
                ->getVehiclesByIds($getVehicleByIdsRequest);

            $data = array_merge($data, $response->getData());
        }

        return $this->mapper->mapVehicles($data);
    }

    /**
     * @param GetLinkageTargetsQuery $getLinkageTargetsQuery
     *
     * @return ArrayCollection<int, LinkageTargetDetails>
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getLinkageTargets(GetLinkageTargetsQuery $getLinkageTargetsQuery): ArrayCollection
    {
        $linkageTargets = [];

        do {
            $response = $this
                ->tecDocApi
                ->getLinkageTargets($getLinkageTargetsQuery);

            $linkageTargets = array_merge($linkageTargets, $response->getLinkageTargets());

            $getLinkageTargetsQuery->setPage($getLinkageTargetsQuery->getPage() + 1);
        } while (!empty($response->getLinkageTargets()));

        return $this->mapper->mapLinkageTargets($linkageTargets);
    }

    /**
     * @param GetVehiclesByKeyNumberPlatesQuery $getVehiclesByKeyNumberPlatesQuery
     *
     * @return ArrayCollection<int,VehicleByKeyNumberPlates>
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     * @throws MappingError
     */
    public function getVehiclesByKeyNumberPlates(GetVehiclesByKeyNumberPlatesQuery $getVehiclesByKeyNumberPlatesQuery): ArrayCollection
    {
        $response = $this
            ->tecDocApi
            ->getVehiclesByKeyNumberPlates($getVehiclesByKeyNumberPlatesQuery);

        return $this->mapper->mapVehiclesByKeyNumberPlates($response->getData());
    }
}
