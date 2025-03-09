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

namespace ItPremium\TecDoc\Api\Request;

use ItPremium\TecDoc\Api\Type\AssemblyGroupFacetOptionsType;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GetArticlesRequest extends AbstractTecDocRequest
{
    /**
     * @var string
     */
    protected string $articleCountry;

    /**
     * @var string
     */
    protected string $searchQuery;

    /**
     * @var int
     */
    protected int $searchType = 10;

    /**
     * @var string
     */
    protected string $searchMatchType = 'exact';

    /**
     * @var array|int
     */
    protected array|int $legacyArticleIds;

    /**
     * @var array|int
     */
    protected array|int $dataSupplierIds;

    /**
     * @var array|int
     */
    protected array|int $genericArticleIds;

    /**
     * @var array|int
     */
    protected array|int $assemblyGroupNodeIds;

    /**
     * @var int
     */
    protected int $linkageTargetId;

    /**
     * @var string
     */
    protected string $linkageTargetType;

    /**
     * @var int
     */
    protected int $perPage = 1000;

    /**
     * @var int
     */
    protected int $page = 1;

    /**
     * @var AssemblyGroupFacetOptionsType
     */
    protected AssemblyGroupFacetOptionsType $assemblyGroupFacetOptions;

    /**
     * @var bool
     */
    protected bool $includeMisc = true;

    /**
     * @var bool
     */
    protected bool $includeGenericArticles = true;

    /**
     * @var bool
     */
    protected bool $includeOEMNumbers = true;

    /**
     * @var bool
     */
    protected bool $includeReplacedByArticles = true;

    /**
     * @var bool
     */
    protected bool $includeArticleCriteria = true;

    /**
     * @var bool
     */
    protected bool $includeImages = true;

    /**
     * @return string
     */
    public function getArticleCountry(): string
    {
        return $this->articleCountry;
    }

    /**
     * @param string $articleCountry
     *
     * @return $this;
     */
    public function setArticleCountry(string $articleCountry): static
    {
        $this->articleCountry = $articleCountry;

        return $this;
    }

    /**
     * @return string
     */
    public function getSearchQuery(): string
    {
        return $this->searchQuery;
    }

    /**
     * @param string $searchQuery
     *
     * @return $this;
     */
    public function setSearchQuery(string $searchQuery): static
    {
        $this->searchQuery = $searchQuery;

        return $this;
    }

    /**
     * @return int
     */
    public function getSearchType(): int
    {
        return $this->searchType;
    }

    /**
     * @param int $searchType
     *
     * @return $this;
     */
    public function setSearchType(int $searchType): static
    {
        $this->searchType = $searchType;

        return $this;
    }

    /**
     * @return string
     */
    public function getSearchMatchType(): string
    {
        return $this->searchMatchType;
    }

    /**
     * @param string $searchMatchType
     *
     * @return $this;
     */
    public function setSearchMatchType(string $searchMatchType): static
    {
        $this->searchMatchType = $searchMatchType;

        return $this;
    }

    /**
     * @return array|int
     */
    public function getLegacyArticleIds(): array|int
    {
        return $this->legacyArticleIds;
    }

    /**
     * @param array|int $legacyArticleIds
     *
     * @return $this;
     */
    public function setLegacyArticleIds(array|int $legacyArticleIds): static
    {
        $this->legacyArticleIds = $legacyArticleIds;

        return $this;
    }

    /**
     * @return array|int
     */
    public function getDataSupplierIds(): array|int
    {
        return $this->dataSupplierIds;
    }

    /**
     * @param array|int $dataSupplierIds
     *
     * @return $this;
     */
    public function setDataSupplierIds(array|int $dataSupplierIds): static
    {
        $this->dataSupplierIds = $dataSupplierIds;

        return $this;
    }

    /**
     * @return array|int
     */
    public function getAssemblyGroupNodeIds(): array|int
    {
        return $this->genericArticleIds;
    }

    /**
     * @param array|int $genericArticleIds
     *
     * @return $this;
     */
    public function setGenericArticleIds(array|int $genericArticleIds): static
    {
        $this->genericArticleIds = $genericArticleIds;

        return $this;
    }

    /**
     * @return array|int
     */
    public function getGenericArticleIds(): array|int
    {
        return $this->genericArticleIds;
    }

    /**
     * @param array|int $assemblyGroupNodeIds
     *
     * @return $this;
     */
    public function setAssemblyGroupNodeIds(array|int $assemblyGroupNodeIds): static
    {
        $this->assemblyGroupNodeIds = $assemblyGroupNodeIds;

        return $this;
    }

    /**
     * @return int
     */
    public function getLinkageTargetId(): int
    {
        return $this->linkageTargetId;
    }

    /**
     * @param int $linkageTargetId
     *
     * @return $this;
     */
    public function setLinkageTargetId(int $linkageTargetId): static
    {
        $this->linkageTargetId = $linkageTargetId;

        return $this;
    }

    /**
     * @return string
     */
    public function getLinkageTargetType(): string
    {
        return $this->linkageTargetType;
    }

    /**
     * @param string $linkageTargetType
     *
     * @return $this;
     */
    public function setLinkageTargetType(string $linkageTargetType): static
    {
        $this->linkageTargetType = $linkageTargetType;

        return $this;
    }

    /**
     * @return int
     */
    public function getPerPage(): int
    {
        return $this->perPage;
    }

    /**
     * @param int $perPage
     *
     * @return $this;
     */
    public function setPerPage(int $perPage): static
    {
        $this->perPage = $perPage;

        return $this;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @param int $page
     *
     * @return $this;
     */
    public function setPage(int $page): static
    {
        $this->page = $page;

        return $this;
    }

    /**
     * @return AssemblyGroupFacetOptionsType
     */
    public function getAssemblyGroupFacetOptions(): AssemblyGroupFacetOptionsType
    {
        return $this->assemblyGroupFacetOptions;
    }

    /**
     * @param AssemblyGroupFacetOptionsType $assemblyGroupFacetOptions
     *
     * @return $this;
     */
    public function setAssemblyGroupFacetOptions(AssemblyGroupFacetOptionsType $assemblyGroupFacetOptions): static
    {
        $this->assemblyGroupFacetOptions = $assemblyGroupFacetOptions;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIncludeMisc(): bool
    {
        return $this->includeMisc;
    }

    /**
     * @param bool $includeMisc
     *
     * @return $this;
     */
    public function setIncludeMisc(bool $includeMisc): static
    {
        $this->includeMisc = $includeMisc;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIncludeGenericArticles(): bool
    {
        return $this->includeGenericArticles;
    }

    /**
     * @param bool $includeGenericArticles
     *
     * @return $this;
     */
    public function setIncludeGenericArticles(bool $includeGenericArticles): static
    {
        $this->includeGenericArticles = $includeGenericArticles;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIncludeOEMNumbers(): bool
    {
        return $this->includeOEMNumbers;
    }

    /**
     * @param bool $includeOEMNumbers
     *
     * @return $this;
     */
    public function setIncludeOEMNumbers(bool $includeOEMNumbers): static
    {
        $this->includeOEMNumbers = $includeOEMNumbers;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIncludeReplacedByArticles(): bool
    {
        return $this->includeReplacedByArticles;
    }

    /**
     * @param bool $includeReplacedByArticles
     *
     * @return $this;
     */
    public function setIncludeReplacedByArticles(bool $includeReplacedByArticles): static
    {
        $this->includeReplacedByArticles = $includeReplacedByArticles;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIncludeArticleCriteria(): bool
    {
        return $this->includeArticleCriteria;
    }

    /**
     * @param bool $includeArticleCriteria
     *
     * @return $this;
     */
    public function setIncludeArticleCriteria(bool $includeArticleCriteria): static
    {
        $this->includeArticleCriteria = $includeArticleCriteria;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIncludeImages(): bool
    {
        return $this->includeImages;
    }

    /**
     * @param bool $includeImages
     *
     * @return $this;
     */
    public function setIncludeImages(bool $includeImages): static
    {
        $this->includeImages = $includeImages;

        return $this;
    }
}
