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

use CuyZ\Valinor\Mapper\MappingError;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use GuzzleHttp\Exception\GuzzleException;
use ItPremium\TecDoc\Api\Exception\TecDocApiException;
use ItPremium\TecDoc\Api\Type\LinkageTargetTypeAndId;
use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Enum\LinkingTargetType;
use ItPremium\TecDoc\Model\Data\LinkageTarget\LinkageTargetDetails;
use ItPremium\TecDoc\Model\Data\VehicleByKeyNumberPlates;
use ItPremium\TecDoc\Model\Query\GetLinkageTargetsQuery;
use ItPremium\TecDoc\Model\Query\GetVehiclesByKeyNumberPlatesQuery;
use ItPremium\TecDoc\Repository\Api\VehicleRepository;
use Symfony\Component\Cache\Exception\CacheException;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class VehicleService
{
    /**
     * VehicleService constructor.
     *
     * @param VehicleRepository $vehicleRepository
     */
    public function __construct(
        private readonly VehicleRepository $vehicleRepository,
    ) {
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
        $minYear = \Configuration::get(ConfigurationConstant::TECDOC_MIN_MODEL_YEAR);

        $linkingTargets = $this
            ->vehicleRepository
            ->getLinkageTargets($getLinkageTargetsQuery);

        $criteria = Criteria::create()
            ->orderBy(['fuelType' => Criteria::ASC, 'description' => Criteria::ASC]);

        return $linkingTargets->filter(function (LinkageTargetDetails $linkageTargetDetails) use ($minYear) {
            return !$linkageTargetDetails->yearFrom || $linkageTargetDetails->yearFrom >= $minYear;
        })->matching($criteria);
    }

    /**
     * @param int $vehicleId
     * @param LinkingTargetType $linkingTargetType
     *
     * @return LinkageTargetDetails|bool
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getVehicleById(int $vehicleId, LinkingTargetType $linkingTargetType = LinkingTargetType::PASSENGER): LinkageTargetDetails|bool
    {
        $linkageTargetTypeAndId = (new LinkageTargetTypeAndId())
            ->setType($linkingTargetType->value)
            ->setId($vehicleId);

        $getLinkageTargetsQuery = (new GetLinkageTargetsQuery())
            ->setLinkageTargetType($linkingTargetType->value)
            ->setLinkageTargetIds($linkageTargetTypeAndId);

        return $this
            ->getLinkageTargets($getLinkageTargetsQuery)
            ->first();
    }

    /**
     * @param GetVehiclesByKeyNumberPlatesQuery $getVehiclesByKeyNumberPlatesQuery
     *
     * @return ArrayCollection<int, VehicleByKeyNumberPlates>
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     * @throws MappingError
     */
    public function getVehiclesByKeyNumberPlates(GetVehiclesByKeyNumberPlatesQuery $getVehiclesByKeyNumberPlatesQuery): ArrayCollection
    {
        return $this
            ->vehicleRepository
            ->getVehiclesByKeyNumberPlates($getVehiclesByKeyNumberPlatesQuery);
    }
}
