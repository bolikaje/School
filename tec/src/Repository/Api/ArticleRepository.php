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
use ItPremium\TecDoc\Api\Request\GetArticleLinkedAllLinkingTargetRequest;
use ItPremium\TecDoc\Api\Request\GetArticlesRequest;
use ItPremium\TecDoc\Enum\LinkingTargetType;
use ItPremium\TecDoc\Model\Data\Article\Article;
use Symfony\Component\Cache\Exception\CacheException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ArticleRepository extends TecDocApiRepository
{
    /**
     * @param GetArticlesRequest $getArticlesRequest
     * @param bool $simple
     *
     * @return ArrayCollection<int, Article>
     *
     * @throws CacheException
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws MappingError
     */
    public function getArticles(GetArticlesRequest $getArticlesRequest, bool $simple): ArrayCollection
    {
        if ($simple) {
            $getArticlesRequest
                ->setIncludeMisc(false)
                ->setIncludeOEMNumbers(false)
                ->setIncludeReplacedByArticles(false);
        }

        $articles = [];

        do {
            $response = $this
                ->tecDocApi
                ->getArticles($getArticlesRequest);

            $articles = array_merge($articles, $response->getArticles());

            $getArticlesRequest->setPage($getArticlesRequest->getPage() + 1);
        } while ($response->getMaxAllowedPage() >= $getArticlesRequest->getPage());

        return $simple
            ? $this->mapper->mapArticlesSimple($articles)
            : $this->mapper->mapArticles($articles);
    }

    /**
     * @param int $brandId
     * @param string $reference
     *
     * @return Article|bool
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getSingleArticle(int $brandId, string $reference): Article|bool
    {
        $getArticlesRequest = (new GetArticlesRequest())
            ->setSearchQuery($reference)
            ->setDataSupplierIds($brandId);

        $response = $this
            ->tecDocApi
            ->getArticles($getArticlesRequest);

        return $this->mapper->mapArticles($response->getArticles())->first();
    }

    /**
     * @param int $articleId
     * @param int $manufacturerId
     *
     * @return array
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getArticleLinkages(int $articleId, int $manufacturerId): array
    {
        $getArticleLinkagesRequest = (new GetArticleLinkedAllLinkingTargetRequest())
            ->setArticleId($articleId)
            ->setLinkingTargetManuId($manufacturerId)
            ->setLinkingTargetType(LinkingTargetType::PASSENGER->value . LinkingTargetType::COMMERCIAL->value);

        $response = $this
            ->tecDocApi
            ->getArticleLinkages($getArticleLinkagesRequest);

        $rearrangedData = [];

        foreach ($response->getData() as $value) {
            if (isset($value['articleLinkages']['array'])) {
                foreach ($value['articleLinkages']['array'] as $articleLinkage) {
                    $rearrangedData[] = $articleLinkage;
                }
            }
        }

        return $rearrangedData;
    }
}
