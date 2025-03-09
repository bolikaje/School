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
use GuzzleHttp\Exception\GuzzleException;
use ItPremium\TecDoc\Api\Exception\TecDocApiException;
use ItPremium\TecDoc\Model\Data\AssemblyGroup;
use ItPremium\TecDoc\Model\Query\GetArticlesQuery;
use ItPremium\TecDoc\Repository\Api\AssemblyGroupRepository;
use Symfony\Component\Cache\Exception\CacheException;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class AssemblyGroupService
{
    /**
     * AssemblyGroupService constructor.
     *
     * @param AssemblyGroupRepository $assemblyGroupRepository
     */
    public function __construct(
        private readonly AssemblyGroupRepository $assemblyGroupRepository,
    ) {
    }

    /**
     * @param int $assemblyGroupId
     *
     * @return AssemblyGroup|bool
     *
     * @throws CacheException
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     */
    public function getAssemblyGroupById(int $assemblyGroupId): AssemblyGroup|bool
    {
        $assemblyGroups = $this->getNestedAssemblyGroups();

        return $this->findByIdInNestedAssemblyGroups($assemblyGroups, $assemblyGroupId);
    }

    /**
     * @param ArrayCollection $assemblyGroups
     * @param int $assemblyGroupId
     *
     * @return AssemblyGroup|bool
     */
    private function findByIdInNestedAssemblyGroups(ArrayCollection $assemblyGroups, int $assemblyGroupId): AssemblyGroup|bool
    {
        foreach ($assemblyGroups as $assemblyGroup) {
            if ($assemblyGroup->id == $assemblyGroupId) {
                return $assemblyGroup;
            }

            if (!$assemblyGroup->subgroups->isEmpty()) {
                if ($result = $this->findByIdInNestedAssemblyGroups($assemblyGroup->subgroups, $assemblyGroupId)) {
                    return $result;
                }
            }
        }

        return false;
    }

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
        return $this
            ->assemblyGroupRepository
            ->getAssemblyGroups($getArticlesQuery);
    }

    /**
     * @param ?GetArticlesQuery $getArticlesQuery
     *
     * @return ArrayCollection
     *
     * @throws CacheException
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     */
    public function getNestedAssemblyGroups(?GetArticlesQuery $getArticlesQuery = null): ArrayCollection
    {
        return $this->distributeSubgroups(
            $this->buildTree($this->getAssemblyGroups($getArticlesQuery))
        );
    }

    /**
     * @param ArrayCollection<int, AssemblyGroup> $assemblyGroups
     *
     * @return ArrayCollection
     */
    private function distributeSubgroups(ArrayCollection $assemblyGroups): ArrayCollection
    {
        foreach ($assemblyGroups as $assemblyGroup) {
            foreach ($assemblyGroup->subgroups as $subgroup) {
                if ($subgroup->subgroups->isEmpty()) {
                    $assemblyGroup->unsortedSubgroups->add($subgroup);
                } else {
                    $assemblyGroup->sortedSubgroups->add($subgroup);
                }
            }

            $this->distributeSubgroups($assemblyGroup->subgroups);
        }

        return $assemblyGroups;
    }

    /**
     * @param ArrayCollection<int, AssemblyGroup> $assemblyGroups
     * @param ?int $parentNodeId
     * @param ?int $depth
     *
     * @return ArrayCollection<int, AssemblyGroup>
     */
    private function buildTree(ArrayCollection $assemblyGroups, ?int $parentNodeId = null, ?int $depth = 0): ArrayCollection
    {
        $nestedTree = new ArrayCollection();

        foreach ($assemblyGroups as $assemblyGroup) {
            if ($assemblyGroup->parentNodeId == $parentNodeId) {
                $assemblyGroup->depth = $depth;
                $assemblyGroup->subgroups = $this->buildTree($assemblyGroups, $assemblyGroup->id, $depth + 1);

                $nestedTree->add($assemblyGroup);
            }
        }

        return $nestedTree;
    }
}
