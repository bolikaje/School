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

use Doctrine\Common\Collections\ArrayCollection;
use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Enum\BrandQuality;
use ItPremium\TecDoc\Enum\SortOrder;
use ItPremium\TecDoc\Model\Data\Article\Article;
use ItPremium\TecDoc\Model\Filter\ActiveFilter;
use ItPremium\TecDoc\Model\Filter\Facet;
use ItPremium\TecDoc\Model\Filter\Filter;
use KSamuel\FacetedSearch\Index\Factory;
use KSamuel\FacetedSearch\Index\IndexInterface;
use KSamuel\FacetedSearch\Query\AggregationQuery;
use KSamuel\FacetedSearch\Query\SearchQuery;
use PrestaShopBundle\Translation\TranslatorInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class FacetService
{
    private const BRAND_INPUT_NAME = 'brand';
    private const CRITERIA_INPUT_NAME = 'criteria';
    private const DELIVERY_INPUT_NAME = 'delivery';
    private const GENERIC_ARTICLES_INPUT_NAME = 'generic_articles';
    private const IN_STOCK_INPUT_NAME = 'in_stock';
    private const QUALITY_INPUT_NAME = 'quality';

    /**
     * @var ActiveFilter[]
     */
    protected array $activeFilters = [];

    /**
     * @var ArrayCollection<int, Article>
     */
    protected ArrayCollection $articles;

    /**
     * @var Facet[]
     */
    protected array $facets = [];

    /**
     * @var IndexInterface
     */
    private IndexInterface $index;

    /**
     * @var array
     */
    private array $cachedGenericArticles = [];

    /**
     * @var array
     */
    private array $cachedCriteria = [];

    /**
     * @var array
     */
    private array $cachedCriteriaValues = [];

    public function __construct(
        private readonly TranslatorInterface $translator,
    ) {
    }

    /**
     * @param ArrayCollection<int, Article> $articles
     *
     * @return void
     */
    public function init(ArrayCollection $articles): void
    {
        $this->articles = $articles;

        $this->generateIndex();
    }

    /**
     * @return void
     */
    private function generateIndex(): void
    {
        $search = (new Factory())->create(Factory::ARRAY_STORAGE);
        $storage = $search->getStorage();

        $showArticlesWithoutAvailability = \Configuration::get(ConfigurationConstant::TECDOC_SHOW_ARTICLES_WITHOUT_AVAILABILITY);

        $showFacetsForInStock = \Configuration::get(ConfigurationConstant::TECDOC_SHOW_FACETS_FOR_IN_STOCK);
        $showFacetsForGroups = \Configuration::get(ConfigurationConstant::TECDOC_SHOW_FACETS_FOR_GROUPS);
        $showFacetsForBrands = \Configuration::get(ConfigurationConstant::TECDOC_SHOW_FACETS_FOR_BRANDS);
        $showFacetsForDelivery = \Configuration::get(ConfigurationConstant::TECDOC_SHOW_FACETS_FOR_DELIVERY);
        $showFacetsForQuality = \Configuration::get(ConfigurationConstant::TECDOC_SHOW_FACETS_FOR_QUALITY);
        $showFacetsForCriteria = \Configuration::get(ConfigurationConstant::TECDOC_SHOW_FACETS_FOR_CRITERIA);
        $criteriaForFacets = json_decode(\Configuration::get(ConfigurationConstant::TECDOC_CRITERIA_FOR_FACETS));

        $exceptionalCriteriaIds = [];

        foreach ($criteriaForFacets as $criteriaForFacet) {
            if (isset($criteriaForFacet->value)) {
                $exceptionalCriteriaIds[] = $criteriaForFacet->value;
            }
        }

        foreach ($this->articles as $article) {
            $filtrableValues = [];

            if ($showArticlesWithoutAvailability and $showFacetsForInStock) {
                $filtrableValues[self::IN_STOCK_INPUT_NAME] = !$article->availability->isEmpty();
            }

            if ($showFacetsForGroups and !$article->genericArticles->isEmpty()) {
                $genericArticle = $article->genericArticles->first();
                $filtrableValues[self::GENERIC_ARTICLES_INPUT_NAME] = $genericArticle->id;

                if (!isset($this->cachedGenericArticles[$genericArticle->id])) {
                    $this->cachedGenericArticles[$genericArticle->id] = $genericArticle->description;
                }
            }

            if ($showFacetsForBrands) {
                $filtrableValues[self::BRAND_INPUT_NAME] = $article->brandName;
            }

            if ($showFacetsForDelivery and !$article->availability->isEmpty()) {
                $filtrableValues[self::DELIVERY_INPUT_NAME] = [];

                foreach ($article->availability as $availability) {
                    $filtrableValues[self::DELIVERY_INPUT_NAME][] = $availability->deliveryTime;
                }
            }

            if ($showFacetsForQuality and $article->brandQuality != BrandQuality::NONE) {
                $filtrableValues[self::QUALITY_INPUT_NAME] = $article->brandQuality->value;
            }

            if ($showFacetsForCriteria) {
                foreach ($article->criteria as $criteria) {
                    if (!$criteria->isInterval and in_array($criteria->id, $exceptionalCriteriaIds)) {
                        $criteriaKey = self::CRITERIA_INPUT_NAME . '_' . $criteria->id;
                        $filtrableValues[$criteriaKey] = $criteria->rawValue;

                        if (!isset($this->cachedCriteria[$criteriaKey])) {
                            $this->cachedCriteria[$criteriaKey] = $criteria->description;
                        }

                        $criteriaValueKey = $criteriaKey . '_' . $criteria->rawValue;

                        if (!isset($this->cachedCriteriaValues[$criteriaValueKey])) {
                            $this->cachedCriteriaValues[$criteriaValueKey] = $criteria->formattedValue;
                        }
                    }
                }
            }

            $storage->addRecord($article->getId(), $filtrableValues);
        }

        $this->index = $search;
    }

    /**
     * @return void
     */
    public function generateFacets(): void
    {
        $query = (new AggregationQuery())
            ->filters($this->activeFilters)
            ->countItems()
            ->sort();

        foreach ($this->index->aggregate($query) as $filterKey => $filterValues) {
            $facet = new Facet($this->getFacetLabel($filterKey), (string) $filterKey);

            /* Hide generic articles if there is only one */
            if ($facet->getInputName() == self::GENERIC_ARTICLES_INPUT_NAME and count($filterValues) == 1) {
                continue;
            }

            foreach ($filterValues as $filterValue => $filterCount) {
                $this->addFilterToFacet($facet, $this->getFacetValueLabel($filterKey, $filterValue), (string) $filterValue, (int) $filterCount);
            }

            $this->facets[] = $facet;
        }

        $customFacetsOrder = [
            self::GENERIC_ARTICLES_INPUT_NAME,
            self::BRAND_INPUT_NAME,
            self::IN_STOCK_INPUT_NAME,
            self::DELIVERY_INPUT_NAME,
            self::QUALITY_INPUT_NAME,
        ];

        usort($this->facets, function (Facet $facetA, Facet $facetB) use ($customFacetsOrder) {
            $facetAIndex = in_array($facetA->getInputName(), $customFacetsOrder) ? array_search($facetA->getInputName(), $customFacetsOrder) : PHP_INT_MAX;
            $facetBIndex = in_array($facetB->getInputName(), $customFacetsOrder) ? array_search($facetB->getInputName(), $customFacetsOrder) : PHP_INT_MAX;

            if ($facetAIndex === $facetBIndex) {
                return strcmp($facetB->getInputName(), $facetA->getInputName());
            }

            return $facetAIndex <=> $facetBIndex;
        });
    }

    /**
     * @param mixed $filterKey
     *
     * @return string
     */
    private function getFacetLabel(mixed $filterKey): string
    {
        $facetLabel = match ($filterKey) {
            self::BRAND_INPUT_NAME => $this->translator->trans('Brand', [], 'Modules.Itptecdoc.Shop'),
            self::DELIVERY_INPUT_NAME => $this->translator->trans('Delivery', [], 'Modules.Itptecdoc.Shop'),
            self::GENERIC_ARTICLES_INPUT_NAME => $this->translator->trans('Groups', [], 'Modules.Itptecdoc.Shop'),
            self::IN_STOCK_INPUT_NAME => $this->translator->trans('In stock', [], 'Modules.Itptecdoc.Shop'),
            self::QUALITY_INPUT_NAME => $this->translator->trans('Quality', [], 'Modules.Itptecdoc.Shop'),
            default => $this->cachedCriteria[$filterKey] ?? $filterKey,
        };

        return (string) $facetLabel;
    }

    /**
     * @param mixed $filterKey
     * @param mixed $filterValue
     *
     * @return string
     */
    private function getFacetValueLabel(mixed $filterKey, mixed $filterValue): string
    {
        $facetValueLabel = match ($filterKey) {
            self::BRAND_INPUT_NAME => $filterValue,
            self::DELIVERY_INPUT_NAME => (int) $filterValue === 1
                ? $this->translator->trans('Up to 1 working day', [], 'Modules.Itptecdoc.Shop')
                : $this->translator->trans('Up to %s working days', [$filterValue], 'Modules.Itptecdoc.Shop'),
            self::GENERIC_ARTICLES_INPUT_NAME => $this->cachedGenericArticles[$filterValue],
            self::IN_STOCK_INPUT_NAME => (bool) $filterValue
                ? $this->translator->trans('Yes', [], 'Modules.Itptecdoc.Shop')
                : $this->translator->trans('No', [], 'Modules.Itptecdoc.Shop'),
            self::QUALITY_INPUT_NAME => BrandQuality::tryFrom($filterValue)->label(),
            default => $this->cachedCriteriaValues[$filterKey . '_' . $filterValue] ?? $filterValue,
        };

        return (string) $facetValueLabel;
    }

    /**
     * @param Facet $facet
     * @param string $filterLabel
     * @param mixed $filterValue
     * @param int $filterCount
     *
     * @return void
     */
    private function addFilterToFacet(Facet $facet, string $filterLabel, mixed $filterValue, int $filterCount): void
    {
        $active = $this->isFilterActive($facet->getInputName(), $filterValue);

        $facet->addFilter(
            new Filter($filterLabel, $filterValue, $filterCount, $active)
        );
    }

    /**
     * @param string $filterName
     * @param mixed $filterValue
     *
     * @return bool
     */
    private function isFilterActive(string $filterName, mixed $filterValue): bool
    {
        foreach ($this->activeFilters as $activeFilter) {
            if ($activeFilter->getFieldName() == $filterName and $activeFilter->hasValue($filterValue)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $inputName
     * @param mixed $values
     *
     * @return FacetService
     */
    public function addActiveFilter(string $inputName, mixed $values): FacetService
    {
        $this->activeFilters[] = new ActiveFilter($inputName, $values);

        return $this;
    }

    /**
     * @return ArrayCollection<int, Article>
     */
    public function filterArticles(): ArrayCollection
    {
        $query = (new SearchQuery())->filters($this->activeFilters);

        $data = $this->index->query($query);

        return $this->articles->filter(function (Article $article) use ($data) {
            return in_array($article->getId(), $data);
        });
    }

    /**
     * @param ArrayCollection<int, Article> $articles
     * @param SortOrder $sortOrder
     *
     * @return ArrayCollection<int, Article>
     *
     * @throws \Exception
     */
    public function sort(ArrayCollection $articles, SortOrder $sortOrder = SortOrder::CHEAPEST): ArrayCollection
    {
        $withPrice = [];
        $withoutPrice = [];

        foreach ($articles as $article) {
            if ($article->getMinimumPrice()) {
                $withPrice[] = $article;
            } else {
                $withoutPrice[] = $article;
            }
        }

        usort($withPrice, function (Article $articleA, Article $articleB) use ($sortOrder) {
            $priceA = $articleA->getMinimumPrice();
            $priceB = $articleB->getMinimumPrice();

            return $sortOrder === SortOrder::EXPENSIVE
                ? $priceB <=> $priceA
                : $priceA <=> $priceB;
        });

        $sortedArticles = array_merge($withPrice, $withoutPrice);

        return new ArrayCollection($sortedArticles);
    }

    /**
     * @return Facet[]
     */
    public function getFacets(): array
    {
        return $this->facets;
    }
}
