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
use ItPremium\TecDoc\Api\Type\AssemblyGroupFacetOptionsType;
use ItPremium\TecDoc\Enum\AssemblyGroupType;
use ItPremium\TecDoc\Model\Data\AssemblyGroup;
use ItPremium\TecDoc\Model\Query\GetArticlesQuery;
use Symfony\Component\Cache\Exception\CacheException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AssemblyGroupRepository extends TecDocApiRepository
{
    /**
     * @param ?GetArticlesQuery $getArticlesQuery
     *
     * @return ArrayCollection<int, AssemblyGroup>
     *
     * @throws CacheException
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     */
    public function getAssemblyGroups(?GetArticlesQuery $getArticlesQuery = null): ArrayCollection
    {
        if (!$getArticlesQuery) {
            $assemblyGroupTypes = AssemblyGroupType::values();

            $assemblyGroupFacetOptionType = (new AssemblyGroupFacetOptionsType())
                ->setAssemblyGroupType(implode('', $assemblyGroupTypes));

            $getArticlesQuery = (new GetArticlesQuery())
                ->setAssemblyGroupFacetOptions($assemblyGroupFacetOptionType);
        }

        $getArticlesQuery
            ->setPerPage(0)
            ->setPage(1);

        $assemblyGroupFacets = $this
            ->tecDocApi
            ->getArticles($getArticlesQuery)
            ->getAssemblyGroupFacets();

        return $this->mapper->mapAssemblyGroups($assemblyGroupFacets['counts']);
    }
}
