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

namespace ItPremium\TecDoc\Api\Response;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GetArticlesResponse extends AbstractTecDocResponse
{
    /**
     * @var int
     */
    protected int $totalMatchingArticles;

    /**
     * @var int
     */
    protected int $maxAllowedPage;

    /**
     * @var array
     */
    protected array $articles = [];

    /**
     * @var array
     */
    protected array $dataSupplierFacets = [];

    /**
     * @var array
     */
    protected array $genericArticleFacets = [];

    /**
     * @var array
     */
    protected array $criteriaFacets = [];

    /**
     * @var array
     */
    protected array $assemblyGroupFacets = [];

    /**
     * @var array
     */
    protected array $articleStatusFacets = [];

    /**
     * @return int
     */
    public function getTotalMatchingArticles(): int
    {
        return $this->totalMatchingArticles;
    }

    /**
     * @param int $totalMatchingArticles
     *
     * @return $this
     */
    public function setTotalMatchingArticles(int $totalMatchingArticles): static
    {
        $this->totalMatchingArticles = $totalMatchingArticles;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxAllowedPage(): int
    {
        return $this->maxAllowedPage;
    }

    /**
     * @param int $maxAllowedPage
     *
     * @return $this
     */
    public function setMaxAllowedPage(int $maxAllowedPage): static
    {
        $this->maxAllowedPage = $maxAllowedPage;

        return $this;
    }

    /**
     * @return array
     */
    public function getArticles(): array
    {
        return $this->articles;
    }

    /**
     * @param array $articles
     *
     * @return $this
     */
    public function setArticles(array $articles): static
    {
        $this->articles = $articles;

        return $this;
    }

    /**
     * @return array
     */
    public function getDataSupplierFacets(): array
    {
        return $this->dataSupplierFacets;
    }

    /**
     * @param array $dataSupplierFacets
     *
     * @return $this
     */
    public function setDataSupplierFacets(array $dataSupplierFacets): static
    {
        $this->dataSupplierFacets = $dataSupplierFacets;

        return $this;
    }

    /**
     * @return array
     */
    public function getGenericArticleFacets(): array
    {
        return $this->genericArticleFacets;
    }

    /**
     * @param array $genericArticleFacets
     *
     * @return $this
     */
    public function setGenericArticleFacets(array $genericArticleFacets): static
    {
        $this->genericArticleFacets = $genericArticleFacets;

        return $this;
    }

    /**
     * @return array
     */
    public function getCriteriaFacets(): array
    {
        return $this->criteriaFacets;
    }

    /**
     * @param array $criteriaFacets
     *
     * @return $this
     */
    public function setCriteriaFacets(array $criteriaFacets): static
    {
        $this->criteriaFacets = $criteriaFacets;

        return $this;
    }

    /**
     * @return array
     */
    public function getAssemblyGroupFacets(): array
    {
        return $this->assemblyGroupFacets;
    }

    /**
     * @param array $assemblyGroupFacets
     *
     * @return $this
     */
    public function setAssemblyGroupFacets(array $assemblyGroupFacets): static
    {
        $this->assemblyGroupFacets = $assemblyGroupFacets;

        return $this;
    }

    /**
     * @return array
     */
    public function getArticleStatusFacets(): array
    {
        return $this->articleStatusFacets;
    }

    /**
     * @param array $articleStatusFacets
     *
     * @return $this
     */
    public function setArticleStatusFacets(array $articleStatusFacets): static
    {
        $this->articleStatusFacets = $articleStatusFacets;

        return $this;
    }
}
