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
use ItPremium\TecDoc\Enum\BrandQuality;
use ItPremium\TecDoc\Model\Data\Brand;
use ItPremium\TecDoc\Repository\Api\BrandRepository;
use ItPremium\TecDoc\Repository\BrandStatusRepository;
use ItPremium\TecDoc\Service\Trait\Activatable;
use Symfony\Component\Cache\Exception\CacheException;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class BrandService
{
    use Activatable;

    /**
     * BrandService constructor.
     *
     * @param BrandRepository $brandRepository
     * @param BrandStatusRepository $brandStatusRepository
     */
    public function __construct(
        private readonly BrandRepository $brandRepository,
        private readonly BrandStatusRepository $brandStatusRepository,
    ) {
    }

    /**
     * @param bool $active
     *
     * @return ArrayCollection<int, Brand>
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getBrands(bool $active = true): ArrayCollection
    {
        $this->models = $this
            ->brandRepository
            ->getBrands();

        return $this->prepareBrands($active);
    }

    /**
     * @param int $brandId
     * @param bool $active
     *
     * @return Brand|bool
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getBrandById(int $brandId, bool $active = true): Brand|bool
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('id', $brandId));

        return $this
            ->getBrands($active)
            ->matching($criteria)
            ->first();
    }

    /**
     * @param string $slug
     *
     * @return Brand|bool
     *
     * @throws CacheException
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     */
    public function getBrandBySlug(string $slug): Brand|bool
    {
        $brands = $this->getBrands();

        return $brands->filter(function (Brand $brand) use ($slug) {
            return $brand->getSlug() === $slug;
        })->first();
    }

    /**
     * @param bool $active
     *
     * @return ArrayCollection<int, Brand>
     */
    private function prepareBrands(bool $active): ArrayCollection
    {
        $this->processDeactivated(
            $this->brandStatusRepository->getDeactivatedBrandsIds()
        );

        $brandRatings = $this->brandStatusRepository->getBrandsQuality();

        foreach ($this->models as $brand) {
            $key = array_search($brand->id, array_column($brandRatings, 'tecdocBrandId'));

            if ($key !== false) {
                $brand->quality = BrandQuality::tryFrom($brandRatings[$key]['quality']);
            }
        }

        return $this->getModels($active);
    }
}
