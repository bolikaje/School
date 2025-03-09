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
use ItPremium\TecDoc\Enum\LinkingTargetType;
use ItPremium\TecDoc\Model\Data\Manufacturer;
use ItPremium\TecDoc\Repository\Api\ManufacturerRepository;
use ItPremium\TecDoc\Repository\ManufacturerStatusRepository;
use ItPremium\TecDoc\Service\Trait\Activatable;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class ManufacturerService
{
    use Activatable;

    /**
     * ManufacturerService constructor.
     *
     * @param ManufacturerRepository $manufacturerRepository
     * @param ManufacturerStatusRepository $manufacturerStatusRepository
     */
    public function __construct(
        private readonly ManufacturerRepository $manufacturerRepository,
        private readonly ManufacturerStatusRepository $manufacturerStatusRepository,
    ) {
    }

    /**
     * @param int $manufacturerId
     * @param LinkingTargetType $linkingTargetType
     *
     * @return Manufacturer|bool
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     */
    public function getManufacturerById(int $manufacturerId, LinkingTargetType $linkingTargetType = LinkingTargetType::PASSENGER): Manufacturer|bool
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('id', $manufacturerId));

        return $this
            ->getManufacturers(true, $linkingTargetType)
            ->matching($criteria)
            ->first();
    }

    /**
     * @param string $slug
     * @param LinkingTargetType $linkingTargetType
     *
     * @return Manufacturer|bool
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     */
    public function getManufacturerBySlug(string $slug, LinkingTargetType $linkingTargetType): Manufacturer|bool
    {
        $manufacturers = $this
            ->getManufacturers(true, $linkingTargetType);

        return $manufacturers->filter(function (Manufacturer $manufacturer) use ($slug) {
            return $manufacturer->getSlug() === $slug;
        })->first();
    }

    /**
     * @param bool $active
     * @param LinkingTargetType $linkingTargetType
     *
     * @return ArrayCollection<int, Manufacturer>
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws \Exception
     */
    public function getManufacturers(bool $active = true, LinkingTargetType $linkingTargetType = LinkingTargetType::PASSENGER): ArrayCollection
    {
        $this->models = $this
            ->manufacturerRepository
            ->getManufacturers($linkingTargetType);

        return $this->prepareManufacturers($active);
    }

    /**
     * @param bool $active
     *
     * @return ArrayCollection<int, Manufacturer>
     *
     * @throws \Exception
     */
    private function prepareManufacturers(bool $active): ArrayCollection
    {
        $this->processDeactivated(
            $this->manufacturerStatusRepository->getDeactivatedManufacturersIds()
        );

        $criteria = Criteria::create()
            ->orderBy(['name' => Criteria::ASC]);

        $collection = $this
            ->getModels($active)
            ->matching($criteria);

        return new ArrayCollection(
            array_values($collection->toArray())
        );
    }

    /**
     * @param int $articleId
     *
     * @return ArrayCollection<int, Manufacturer>
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws \Exception
     */
    public function getArticleLinkedManufacturers(int $articleId): ArrayCollection
    {
        $this->models = $this
            ->manufacturerRepository
            ->getArticleLinkedManufacturers($articleId);

        return $this->prepareManufacturers(true);
    }

    /**
     * @param ArrayCollection<int, Manufacturer> $manufacturers
     *
     * @return ArrayCollection
     */
    public function generateAlphabeticalFilter(ArrayCollection $manufacturers): ArrayCollection
    {
        $alphabeticalFilter = [];

        foreach ($manufacturers as $manufacturer) {
            $firstLetter = mb_strtoupper(mb_substr($manufacturer->name, 0, 1));

            if (!isset($alphabeticalFilter[$firstLetter])) {
                $alphabeticalFilter[$firstLetter] = true;
            }
        }

        ksort($alphabeticalFilter);

        return new ArrayCollection(array_keys($alphabeticalFilter));
    }
}
