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

namespace ItPremium\TecDoc\Model\Data\Article;

use Doctrine\Common\Collections\ArrayCollection;
use ItPremium\TecDoc\Enum\ArticleType;
use ItPremium\TecDoc\Enum\BrandQuality;
use ItPremium\TecDoc\Model\Data\Criteria\Criteria;
use ItPremium\TecDoc\Model\Data\Criteria\CriteriaValue;
use ItPremium\TecDoc\Model\Data\Criteria\GroupedCriteria;
use ItPremium\TecDoc\Model\Data\ImageRecord;
use ItPremium\TecDoc\Utils\Helper;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Article
{
    /**
     * @var ArticleType
     */
    protected ArticleType $articleType = ArticleType::TECDOC_ARTICLE;

    /**
     * Article constructor.
     *
     * @param int $brandId
     * @param string $brandName
     * @param string $reference
     * @param ArrayCollection<int, ArticleStock> $availability
     * @param ArrayCollection<int, Criteria> $criteria
     * @param ArrayCollection<int, GenericArticle> $genericArticles
     * @param ArrayCollection<int, ImageRecord> $images
     * @param ArrayCollection<int, ArticleOemNumber> $oemNumbers
     * @param ArrayCollection<int, ArticleReplacement> $replacements
     */
    public function __construct(
        /** @var int */
        public int $brandId,

        /** @var string */
        public string $brandName,

        /** @var string */
        public string $reference,

        /** @var ArrayCollection<int, ArticleStock> */
        public readonly ArrayCollection $availability = new ArrayCollection(),

        /** @var ArrayCollection<int, Criteria> */
        public ArrayCollection $criteria = new ArrayCollection(),

        /** @var ArrayCollection<int, GenericArticle> */
        public readonly ArrayCollection $genericArticles = new ArrayCollection(),

        /** @var ArrayCollection<int, ImageRecord> */
        public readonly ArrayCollection $images = new ArrayCollection(),

        /** @var ArrayCollection<int, ArticleOemNumber> */
        public readonly ArrayCollection $oemNumbers = new ArrayCollection(),

        /** @var ArrayCollection<int, ArticleReplacement> */
        public readonly ArrayCollection $replacements = new ArrayCollection(),
    ) {
    }

    /**
     * @var BrandQuality
     */
    public BrandQuality $brandQuality = BrandQuality::NONE;

    /**
     * @var bool
     */
    public bool $oem = false;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->genericArticles->first()->legacyArticleId;
    }

    /**
     * @return ArticleType
     */
    public function getType(): ArticleType
    {
        return $this->articleType;
    }

    /**
     * @param bool $fallback
     *
     * @return string
     */
    public function getName(bool $fallback = true): string
    {
        return $this->genericArticles->first()->description ?: ($fallback ? $this->getAlternativeName() : '');
    }

    /**
     * @return string
     */
    public function getAlternativeName(): string
    {
        return $this->brandName . ' - ' . $this->reference;
    }

    /**
     * @return string
     */
    public function getCoverImage(): string
    {
        if (!$this->images->isEmpty()) {
            return $this->images->first()->getImageUrl();
        }

        return _MODULE_DIR_ . 'itp_tecdoc/views/img/no-image.jpg';
    }

    /**
     * @return ArrayCollection
     */
    public function getGroupedCriteria(): ArrayCollection
    {
        $groupedCriteria = [];

        foreach ($this->criteria as $criteria) {
            if (!isset($groupedCriteria[$criteria->id])) {
                $groupedCriteria[$criteria->id] = new GroupedCriteria($criteria->id, $criteria->description, $criteria->type, $criteria->isInterval, $criteria->isMandatory);
            }

            if ($criteria->formattedValue) {
                $groupedCriteria[$criteria->id]->addCriteriaValue(
                    new CriteriaValue($criteria->rawValue, $criteria->formattedValue)
                );
            }
        }

        return new ArrayCollection($groupedCriteria);
    }

    /**
     * @return string
     */
    public function getArticleSlug(): string
    {
        return \Tools::str2url($this->getName(false));
    }

    /**
     * @return string
     */
    public function getBrandSlug(): string
    {
        return \Tools::str2url($this->brandName);
    }

    /**
     * @return string
     */
    public function getReferenceEncoded(): string
    {
        return Helper::urlEncode($this->reference);
    }

    /**
     * @param ?int $langId
     *
     * @return string
     *
     * @throws \PrestaShopException
     */
    public function getLink(?int $langId = null): string
    {
        if (!$langId) {
            $langId = \Context::getContext()->language->id;
        }

        $dispatcher = \Dispatcher::getInstance();

        $params = [
            'brand_slug' => $this->getBrandSlug(),
            'reference' => $this->getReferenceEncoded(),
        ];

        if ($dispatcher->hasKeyword('module-itp_tecdoc-article', $langId, 'article_slug')) {
            $params['article_slug'] = $this->getArticleSlug();
        }

        return \Context::getContext()->link->getModuleLink('itp_tecdoc', 'article', $params, true, $langId);
    }

    /**
     * @return float|bool
     */
    public function getMinimumPrice(): float|bool
    {
        if (!$this->availability->isEmpty()) {
            $prices = $this->availability->map(function (ArticleStock $articleStock) {
                return $articleStock->prices->priceWithReductionsWithoutTax;
            })->toArray();

            return min($prices);
        }

        return false;
    }
}
