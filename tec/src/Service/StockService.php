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
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\NonUniqueResultException;
use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Entity\Doctrine\Interface\TecdocEntityWithRateInterface;
use ItPremium\TecDoc\Entity\Doctrine\TecdocStock;
use ItPremium\TecDoc\Model\Data\Article\Article;
use ItPremium\TecDoc\Model\Data\Article\ArticleStock;
use ItPremium\TecDoc\Model\Data\Article\ArticleStockGroupDiscount;
use ItPremium\TecDoc\Model\Data\Article\ArticleStockPrice;
use ItPremium\TecDoc\Repository\DiscountRepository;
use ItPremium\TecDoc\Repository\MarginRepository;
use ItPremium\TecDoc\Repository\StockRepository;
use ItPremium\TecDoc\Utils\Helper;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class StockService
{
    /**
     * @var array|int[]
     */
    private array $prioritize = [
        'supplier' => 1,
        'price_range' => 2,
        'brand' => 3,
    ];

    /**
     * StockService constructor.
     *
     * @param StockRepository $stockRepository
     * @param MarginRepository $marginRepository
     * @param DiscountRepository $discountRepository
     */
    public function __construct(
        private readonly StockRepository $stockRepository,
        private readonly MarginRepository $marginRepository,
        private readonly DiscountRepository $discountRepository,
    ) {
    }

    /**
     * @param string $reference
     *
     * @return TecdocStock[]
     */
    public function getStockByReference(string $reference): array
    {
        return $this->stockRepository->findByReference($reference);
    }

    /**
     * @param int $tecdocStockId
     *
     * @return ?TecdocStock
     */
    public function getStockById(int $tecdocStockId): ?TecdocStock
    {
        return $this->stockRepository->find($tecdocStockId);
    }

    /**
     * @param ArrayCollection<int, Article> $articles
     * @param null $addressId
     *
     * @return void
     *
     * @throws \PrestaShopException
     */
    public function setAvailabilityForArticles(ArrayCollection $articles, $addressId = null): void
    {
        if (!$articles->isEmpty()) {
            $taxCalculator = \TaxManagerFactory::getManager(
                \Address::initialize($addressId),
                \Configuration::get(ConfigurationConstant::TECDOC_ID_TAX_RULES_GROUP)
            )->getTaxCalculator();

            $includeTaxes = (new \TaxConfiguration())->includeTaxes();
            $includeCustomerGroupsDiscount = (bool) \Configuration::get(ConfigurationConstant::TECDOC_INCLUDE_CUSTOMER_GROUPS_DISCOUNT);

            $tecdocStocks = $this
                ->stockRepository
                ->findByArticles($articles);

            $tecdocStockMap = [];

            foreach ($tecdocStocks as $tecdocStock) {
                $preparedTecdocStockBrand = mb_strtolower($tecdocStock['brand']);
                $preparedTecdocStockReference = Helper::prepareReference($tecdocStock['reference']);
                $tecdocStockMap[$preparedTecdocStockBrand][$preparedTecdocStockReference][$tecdocStock['id_tecdoc_supplier']] = $tecdocStock;
            }

            foreach ($articles as $article) {
                $preparedTecdocArticleBrand = mb_strtolower($article->brandName);
                $preparedTecdocArticleReference = Helper::prepareReference($article->reference);

                if (isset($tecdocStockMap[$preparedTecdocArticleBrand][$preparedTecdocArticleReference])) {
                    foreach ($tecdocStockMap[$preparedTecdocArticleBrand][$preparedTecdocArticleReference] as $tecdocStock) {
                        $tecdocStock = TecdocStock::fromArray($tecdocStock);

                        $articleStock = new ArticleStock(
                            $tecdocStock->getTecdocSupplier()->getId(),
                            $tecdocStock->getMinimumOrderQuantity(),
                            $tecdocStock->getEnforceQuantityMultiple(),
                            $tecdocStock->getStock(),
                            $tecdocStock->getDeliveryTime(),
                            date('Y-m-d H:i:s', strtotime('+ ' . $tecdocStock->getDeliveryTime() . ' days')),
                            $tecdocStock->getWeight(),
                            $this->getArticleStockPrice($tecdocStock, $taxCalculator, $includeTaxes, $includeCustomerGroupsDiscount)
                        );

                        $article->availability->add($articleStock);
                    }
                }
            }
        }
    }

    /**
     * @param TecdocStock $tecdocStock
     * @param \TaxCalculator $taxCalculator
     * @param bool $includeTaxes
     * @param bool $includeCustomerGroupsDiscount
     *
     * @return ArticleStockPrice
     */
    private function getArticleStockPrice(TecdocStock $tecdocStock, \TaxCalculator $taxCalculator, bool $includeTaxes, bool $includeCustomerGroupsDiscount = false): ArticleStockPrice
    {
        $articleStockGroupDiscounts = $this->getArticleStockGroupDiscounts($tecdocStock);

        $marginRate = $this->getMarginRate($tecdocStock);
        $discountRate = $this->getDiscountRate($articleStockGroupDiscounts);

        /*
         * Apply margin rule
         */
        $priceWithoutReductionsWithoutTax = round($tecdocStock->getPrice() * (1 + ($marginRate / 100)), 2);
        $priceWithoutReductionsWithTax = $taxCalculator->addTaxes($priceWithoutReductionsWithoutTax);

        /*
         * Apply discount rule
         */
        $priceWithReductionsWithTax = round($priceWithoutReductionsWithTax * ((100 - $discountRate) / 100), 2);

        /*
         * Then we apply the group discount rule set in the PrestaShop configuration
         */
        if ($includeCustomerGroupsDiscount) {
            $groupDiscountRate = \Group::getReductionByIdGroup(
                \Context::getContext()->customer->id_default_group
            );

            if ($groupDiscountRate) {
                $priceWithReductionsWithTax -= ($priceWithReductionsWithTax * $groupDiscountRate / 100);
            }
        }

        $priceWithReductionsWithoutTax = $taxCalculator->removeTaxes($priceWithReductionsWithTax);

        $depositWithoutTax = $tecdocStock->getDeposit();
        $depositWithTax = $taxCalculator->addTaxes($depositWithoutTax);

        $displayedPriceWithoutReductions = $includeTaxes ? $priceWithoutReductionsWithTax : $priceWithoutReductionsWithoutTax;
        $displayedPriceWithReductions = $includeTaxes ? $priceWithReductionsWithTax : $priceWithReductionsWithoutTax;
        $displayedDeposit = $includeTaxes ? $depositWithTax : $depositWithoutTax;

        return new ArticleStockPrice(
            $tecdocStock->getWholesalePrice(),
            $priceWithoutReductionsWithoutTax,
            $priceWithReductionsWithoutTax,
            $priceWithoutReductionsWithTax,
            $priceWithReductionsWithTax,
            $depositWithoutTax,
            $depositWithTax,
            $displayedPriceWithoutReductions,
            $displayedPriceWithReductions,
            $displayedDeposit,
            $discountRate,
            $articleStockGroupDiscounts
        );
    }

    /**
     * @param TecdocStock $tecdocStock
     *
     * @return ArrayCollection<int, ArticleStockGroupDiscount>
     */
    private function getArticleStockGroupDiscounts(TecdocStock $tecdocStock): ArrayCollection
    {
        $records = $this
            ->discountRepository
            ->getDiscountsBySupplierId($tecdocStock->getTecdocSupplier()->getId());

        $articleStockDiscounts = new ArrayCollection();

        /*
         * The "All Groups" discount rule should be considered the primary rule unless there are specific overrides for particular groups with same weight.
         */
        $allGroupsPrioritizedRecord = $this->getPrioritizedRecord($records, $tecdocStock->getBrand(), $tecdocStock->getPrice(), 0);

        if ($allGroupsPrioritizedRecord) {
            $articleStockDiscounts->add(
                new ArticleStockGroupDiscount($allGroupsPrioritizedRecord->getGroupId(), $allGroupsPrioritizedRecord->getRate())
            );
        }

        foreach (\Group::getAllGroupIds() as $group) {
            $prioritizedRecord = $this->getPrioritizedRecord($records, $tecdocStock->getBrand(), $tecdocStock->getPrice(), (int) $group);

            /*
             * When multiple discount rules are applied, including 'All Groups' and specific groups, the rule with the highest weight will be prioritized.
             * However, priority will be given to the group-specific discount rule.
             */
            if ($allGroupsPrioritizedRecord and $prioritizedRecord) {
                if ($prioritizedRecord->getWeight() < $allGroupsPrioritizedRecord->getWeight()) {
                    $prioritizedRecord = $allGroupsPrioritizedRecord;
                }
            /*
             * If a discount rule for a current group is not available, then the discount rule for "All Groups" will be applied
             */
            } elseif ($allGroupsPrioritizedRecord and !$prioritizedRecord) {
                $prioritizedRecord = $allGroupsPrioritizedRecord;
            }

            if ($prioritizedRecord) {
                $articleStockDiscounts->add(
                    new ArticleStockGroupDiscount((int) $group, $prioritizedRecord->getRate())
                );
            }
        }

        return $articleStockDiscounts;
    }

    /**
     * @param TecdocStock $tecdocStock
     *
     * @return float
     */
    public function getMarginRate(TecdocStock $tecdocStock): float
    {
        $records = $this
            ->marginRepository
            ->getMarginsBySupplierId($tecdocStock->getTecdocSupplier()->getId());

        $prioritizedRecord = $this->getPrioritizedRecord($records, $tecdocStock->getBrand(), $tecdocStock->getPrice());

        return $prioritizedRecord ? round($prioritizedRecord->getRate(), 2) : 0;
    }

    /**
     * @param ArrayCollection<int, ArticleStockGroupDiscount> $articleStockGroupDiscounts
     *
     * @return float
     */
    public function getDiscountRate(ArrayCollection $articleStockGroupDiscounts): float
    {
        $criteria = Criteria::create();

        $articleStockGroupDiscount = $articleStockGroupDiscounts
            ->matching($criteria->where(
                Criteria::expr()->eq('groupId', \Context::getContext()->customer->id_default_group)
            ))
            ->first();

        return $articleStockGroupDiscount ? $articleStockGroupDiscount->discountRate : 0;
    }

    /**
     * @param Article $article
     * @param int $tecdocSupplierId
     * @param int $quantityDelta
     *
     * @return void
     *
     * @throws NonUniqueResultException
     */
    public function updateQuantity(Article $article, int $tecdocSupplierId, int $quantityDelta): void
    {
        $stock = $this
            ->stockRepository
            ->findByArticle($article, $tecdocSupplierId);

        if ($stock) {
            $this->stockRepository->save(
                $stock->setStock($stock->getStock() + $quantityDelta)
            );
        }
    }

    /**
     * @param TecdocEntityWithRateInterface[] $records
     * @param string $brand
     * @param float $price
     * @param ?int $groupId
     *
     * @return ?TecdocEntityWithRateInterface
     */
    private function getPrioritizedRecord(array $records, string $brand, float $price, ?int $groupId = null): ?TecdocEntityWithRateInterface
    {
        $tecdocEntityWithRate = null;
        $maxWeight = 0;

        foreach ($records as $record) {
            $weight = 0;

            $loweredBrand = mb_strtolower($brand);
            $recordBrand = mb_strtolower((string) $record->getBrand());
            $recordPriceRangeStart = $record->getPriceRangeStart();
            $recordPriceRangeEnd = $record->getPriceRangeEnd();

            if ($groupId !== null) {
                if ($record->getGroupId() != $groupId) {
                    continue;
                }
            }

            if ($recordBrand and $recordPriceRangeStart > 0 and $recordPriceRangeEnd > 0) {
                if ($recordBrand == $loweredBrand and $price >= $recordPriceRangeStart and $price <= $recordPriceRangeEnd) {
                    $weight += $this->prioritize['price_range'] + $this->prioritize['brand'];
                }
            } elseif ($recordBrand) {
                if ($recordBrand == $loweredBrand) {
                    $weight += $this->prioritize['brand'];
                }
            } elseif ($recordPriceRangeStart > 0 and $recordPriceRangeEnd > 0) {
                if ($price >= $recordPriceRangeStart and $price <= $recordPriceRangeEnd) {
                    $weight += $this->prioritize['price_range'];
                }
            } else {
                $weight += $this->prioritize['supplier'];
            }

            $record->setWeight($weight);

            if ($weight >= $maxWeight) {
                $maxWeight = $weight;
                $tecdocEntityWithRate = $record;
            }
        }

        return $tecdocEntityWithRate;
    }
}
