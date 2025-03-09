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
use ItPremium\TecDoc\Api\Request\GetArticleLinkedManufacturersRequest;
use ItPremium\TecDoc\Api\Request\GetManufacturersRequest;
use ItPremium\TecDoc\Enum\LinkingTargetType;
use ItPremium\TecDoc\Model\Data\Manufacturer;
use Symfony\Component\Cache\Exception\CacheException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ManufacturerRepository extends TecDocApiRepository
{
    /**
     * @param LinkingTargetType $linkingTargetType
     *
     * @return ArrayCollection<int, Manufacturer>
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws MappingError
     * @throws CacheException
     */
    public function getManufacturers(LinkingTargetType $linkingTargetType = LinkingTargetType::PASSENGER): ArrayCollection
    {
        $getManufacturersRequest = (new GetManufacturersRequest())
            ->setLinkingTargetType($linkingTargetType->alternativeValue());

        $response = $this
            ->tecDocApi
            ->getManufacturers($getManufacturersRequest);

        return $this->mapper->mapManufacturers($response->getData());
    }

    /**
     * @param int $articleId
     *
     * @return ArrayCollection<int, Manufacturer>
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws MappingError
     * @throws CacheException
     */
    public function getArticleLinkedManufacturers(int $articleId): ArrayCollection
    {
        $getArticleLinkedManufacturersRequest = (new GetArticleLinkedManufacturersRequest())
            ->setArticleId($articleId)
            ->setLinkingTargetType(LinkingTargetType::PASSENGER->value . LinkingTargetType::COMMERCIAL->value);

        $response = $this
            ->tecDocApi
            ->getArticleLinkedManufacturers($getArticleLinkedManufacturersRequest);

        return $this->mapper->mapManufacturers($response->getData());
    }
}
