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
use ItPremium\TecDoc\Api\Request\GetArticlesRequest;
use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Entity\Doctrine\TecdocStock;
use ItPremium\TecDoc\Enum\ArticleType;
use ItPremium\TecDoc\Enum\BrandQuality;
use ItPremium\TecDoc\Enum\NumberType;
use ItPremium\TecDoc\Enum\SearchMatchType;
use ItPremium\TecDoc\Model\Data\Article\Article;
use ItPremium\TecDoc\Model\Data\Article\CustomArticle;
use ItPremium\TecDoc\Model\Data\Brand;
use ItPremium\TecDoc\Repository\Api\ArticleRepository;
use ItPremium\TecDoc\Repository\Api\VehicleRepository;
use ItPremium\TecDoc\Repository\BrandStatusRepository;
use ItPremium\TecDoc\Utils\Helper;
use Symfony\Component\Cache\Exception\CacheException;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class ArticleService
{
    /**
     * ArticleService constructor.
     *
     * @param ArticleRepository $articleRepository
     * @param BrandService $brandService
     * @param BrandStatusRepository $brandStatusRepository
     * @param StockService $stockService
     * @param VehicleRepository $vehicleRepository
     */
    public function __construct(
        private readonly ArticleRepository $articleRepository,
        private readonly BrandService $brandService,
        private readonly BrandStatusRepository $brandStatusRepository,
        private readonly StockService $stockService,
        private readonly VehicleRepository $vehicleRepository,
    ) {
    }

    /**
     * @param GetArticlesRequest $getArticlesRequest
     * @param bool $simple
     *
     * @return ArrayCollection<int, Article>
     *
     * @throws CacheException
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getArticles(GetArticlesRequest $getArticlesRequest, bool $simple = false): ArrayCollection
    {
        $articles = $this
            ->articleRepository
            ->getArticles($getArticlesRequest, $simple);

        return $this->prepareArticles($articles);
    }

    /**
     * @param string $keyword
     * @param SearchMatchType $searchMatchType
     * @param NumberType $numberType
     *
     * @return ArrayCollection<int, Article>
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws CacheException
     */
    public function getArticlesByKeyword(string $keyword, SearchMatchType $searchMatchType = SearchMatchType::EXACT, NumberType $numberType = NumberType::ANY_NUMBER): ArrayCollection
    {
        $keyword = Helper::prepareKeyword($keyword);

        if (!$keyword or strlen($keyword) < 3) {
            return new ArrayCollection();
        }

        $getArticlesRequest = (new GetArticlesRequest())
            ->setSearchQuery($keyword)
            ->setSearchType($numberType->value)
            ->setSearchMatchType($searchMatchType->value);

        $articles = $this
            ->articleRepository
            ->getArticles($getArticlesRequest, true);

        if (\Configuration::get(ConfigurationConstant::TECDOC_SHOW_CUSTOM_ARTICLES)) {
            $customArticles = $this->getCustomArticlesByKeyword($keyword);

            /*
             * Delete matching custom articles (TecDoc Articles -> Custom Articles)
             */
            foreach ($articles as $article) {
                foreach ($customArticles as $key => $customArticle) {
                    if (
                        mb_strtolower($article->brandName) == mb_strtolower($customArticle->brandName)
                        and mb_strtolower($article->reference) == mb_strtolower($customArticle->reference)
                    ) {
                        unset($customArticles[$key]);
                    }
                }
            }

            $articles = new ArrayCollection(
                array_merge($articles->toArray(), $customArticles->toArray())
            );
        }

        return $this->prepareArticles($articles);
    }

    /**
     * @param int $brandId
     * @param string $reference
     *
     * @return Article|bool
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getSingleArticle(int $brandId, string $reference): Article|bool
    {
        $article = $this
            ->articleRepository
            ->getSingleArticle($brandId, $reference);

        if (!$article) {
            return false;
        }

        return $this->prepareArticles(new ArrayCollection([$article]))->first();
    }

    /**
     * @param int $articleId
     * @param int $manufacturerId
     * @param bool $groupedByModelSeries
     *
     * @return ArrayCollection
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getArticleLinkedVehicles(int $articleId, int $manufacturerId, bool $groupedByModelSeries = false): ArrayCollection
    {
        $articleLinkages = $this
            ->articleRepository
            ->getArticleLinkages($articleId, $manufacturerId);

        $carIds = array_column($articleLinkages, 'linkingTargetId');

        $linkedVehicles = $this
            ->vehicleRepository
            ->getVehiclesByIds($carIds);

        if ($groupedByModelSeries) {
            $groupedVehicles = [];

            foreach ($linkedVehicles as $linkedVehicle) {
                $linkedVehicleKey = $linkedVehicle->details->manufacturerName . ' ' . $linkedVehicle->details->modelName;
                $groupedVehicles[$linkedVehicleKey][] = $linkedVehicle;
            }

            return new ArrayCollection($groupedVehicles);
        }

        return $linkedVehicles;
    }

    /**
     * @param string $keyword
     *
     * @return ArrayCollection<int, CustomArticle>
     */
    public function getCustomArticlesByKeyword(string $keyword): ArrayCollection
    {
        $tecdocStocks = $this
            ->stockService
            ->getStockByReference($keyword);

        $uniqueTecdocStocks = [];

        foreach ($tecdocStocks as $tecdocStock) {
            $uniqueTecdocStockKey = $tecdocStock->getBrand() . '-' . $tecdocStock->getReference();

            if (!isset($uniqueTecdocStocks[$uniqueTecdocStockKey])) {
                $uniqueTecdocStocks[$tecdocStock->getBrand() . '-' . $tecdocStock->getReference()] = $tecdocStock;
            }
        }

        $customArticles = new ArrayCollection();

        foreach ($uniqueTecdocStocks as $uniqueTecdocStock) {
            $customArticles->add(
                $this->createCustomArticleFromStock($uniqueTecdocStock)
            );
        }

        return $customArticles;
    }

    /**
     * @param int $tecdocStockId
     *
     * @return CustomArticle|bool
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getSingleCustomArticle(int $tecdocStockId): CustomArticle|bool
    {
        $tecdocStock = $this
            ->stockService
            ->getStockById($tecdocStockId);

        if (!$tecdocStock) {
            return false;
        }

        $customArticle = $this->createCustomArticleFromStock($tecdocStock);

        return $this->prepareArticles(new ArrayCollection([$customArticle]))->first();
    }

    /**
     * @param TecdocStock $tecdocStock
     *
     * @return CustomArticle
     */
    public function createCustomArticleFromStock(TecdocStock $tecdocStock): CustomArticle
    {
        return new CustomArticle(
            $tecdocStock->getId(),
            $tecdocStock->getBrand(),
            $tecdocStock->getReference(),
            $tecdocStock->getName(),
            $tecdocStock->getOem(),
        );
    }

    /**
     * @param ArrayCollection<int, Article> $articles
     *
     * @return ArrayCollection
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws TecDocApiException
     * @throws CacheException
     */
    private function prepareArticles(ArrayCollection $articles): ArrayCollection
    {
        /**
         * Remove articles for disabled brands
         */
        $disabledBrandsIds = array_map('intval', $this->brandStatusRepository->getDeactivatedBrandsIds());

        $criteria = Criteria::create()->where(
            Criteria::expr()->notIn('brandId', $disabledBrandsIds)
        );

        $articles = $articles->matching($criteria);

        /*
         * Set availability for articles
         */
        // \Hook::exec('actionArticleServicePrepareArticles', ['articles' => $articles]);

        $this->stockService->setAvailabilityForArticles($articles);

        /**
         * Remove articles without availability
         */
        $showArticlesWoAvailability = \Configuration::get(ConfigurationConstant::TECDOC_SHOW_ARTICLES_WITHOUT_AVAILABILITY);

        if (!$showArticlesWoAvailability) {
            $articles = $articles->filter(function (Article $article) {
                return !$article->availability->isEmpty();
            });
        }

        /**
         * Hide empty attributes and sort them
         */
        $criteria = Criteria::create()
            ->where(Criteria::expr()->neq('formattedValue', ''))
            ->orderBy(['id' => Criteria::ASC]);

        foreach ($articles as $article) {
            $article->criteria = $article->criteria->matching($criteria);
        }

        /**
         * Assign brand data
         */
        $brands = $this->brandService->getBrands();
        $brandQualities = $this->brandStatusRepository->getBrandsQuality();

        foreach ($articles as $article) {
            /*
             * Custom articles are coming without brand id, we attempt to add it as it may be useful in future
             */
            if ($article->getType() == ArticleType::CUSTOM_ARTICLE) {
                $brand = $brands->filter(function (Brand $brand) use ($article) {
                    return strtolower($brand->name) == strtolower($article->brandName);
                })->first();

                if ($brand) {
                    $article->brandId = $brand->id;
                }
            }

            /*
             * Assign brand quality
             */
            if ($article->oem) {
                $article->brandQuality = BrandQuality::OEM;
            } else {
                $key = array_search($article->brandId, array_column($brandQualities, 'tecdocBrandId'));

                if ($key !== false) {
                    $article->brandQuality = BrandQuality::tryFrom($brandQualities[$key]['quality']);
                }
            }
        }

        return $articles;
    }
}
