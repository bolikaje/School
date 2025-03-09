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

namespace ItPremium\TecDoc\Api;

use GuzzleHttp\Exception\GuzzleException;
use ItPremium\TecDoc\Api\Exception\TecDocApiException;
use ItPremium\TecDoc\Api\Network\Gateway;
use ItPremium\TecDoc\Api\Request\GetArticleLinkedAllLinkingTargetRequest;
use ItPremium\TecDoc\Api\Request\GetArticleLinkedManufacturersRequest;
use ItPremium\TecDoc\Api\Request\GetArticlesRequest;
use ItPremium\TecDoc\Api\Request\GetBrandsRequest;
use ItPremium\TecDoc\Api\Request\GetCountriesRequest;
use ItPremium\TecDoc\Api\Request\GetCriteriaRequest;
use ItPremium\TecDoc\Api\Request\GetLanguagesRequest;
use ItPremium\TecDoc\Api\Request\GetLinkageTargetsRequest;
use ItPremium\TecDoc\Api\Request\GetManufacturersRequest;
use ItPremium\TecDoc\Api\Request\GetModelSeriesRequest;
use ItPremium\TecDoc\Api\Request\GetVehicleByIdsRequest;
use ItPremium\TecDoc\Api\Request\GetVehicleIdsByCriteriaRequest;
use ItPremium\TecDoc\Api\Request\GetVehiclesByKeyNumberPlatesRequest;
use ItPremium\TecDoc\Api\Request\GetVehiclesByVinRequest;
use ItPremium\TecDoc\Api\Request\GetVersionRequest;
use ItPremium\TecDoc\Api\Response\GetArticleLinkedAllLinkingTargetResponse;
use ItPremium\TecDoc\Api\Response\GetArticleLinkedManufacturersResponse;
use ItPremium\TecDoc\Api\Response\GetArticlesResponse;
use ItPremium\TecDoc\Api\Response\GetBrandsResponse;
use ItPremium\TecDoc\Api\Response\GetCountriesResponse;
use ItPremium\TecDoc\Api\Response\GetCriteriaResponse;
use ItPremium\TecDoc\Api\Response\GetGenericArticlesResponse;
use ItPremium\TecDoc\Api\Response\GetLanguagesResponse;
use ItPremium\TecDoc\Api\Response\GetLinkageTargetsResponse;
use ItPremium\TecDoc\Api\Response\GetManufacturersResponse;
use ItPremium\TecDoc\Api\Response\GetModelSeriesResponse;
use ItPremium\TecDoc\Api\Response\GetVehicleByIdsResponse;
use ItPremium\TecDoc\Api\Response\GetVehicleIdsByCriteriaResponse;
use ItPremium\TecDoc\Api\Response\GetVehiclesByKeyNumberPlatesResponse;
use ItPremium\TecDoc\Api\Response\GetVehiclesByVinResponse;
use ItPremium\TecDoc\Api\Response\GetVersionResponse;
use Symfony\Component\Cache\Exception\CacheException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class TecDocApi
{
    /**
     * TecDocApi constructor.
     *
     * @param Gateway $gateway
     * @param bool $cache
     */
    public function __construct(
        private readonly Gateway $gateway,
        private bool $cache,
    ) {
    }

    /**
     * @return GetVersionResponse
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getVersion(): GetVersionResponse
    {
        $response = $this->gateway->request('getVersion', new GetVersionRequest());

        return GetVersionResponse::fromApiResponse($response);
    }

    /**
     * @return GetLanguagesResponse
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getLanguages(): GetLanguagesResponse
    {
        $response = $this->gateway->request('getLanguages', new GetLanguagesRequest(), $this->cache);

        return GetLanguagesResponse::fromApiResponse($response);
    }

    /**
     * @return GetCountriesResponse
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     */
    public function getCountries(): GetCountriesResponse
    {
        $response = $this->gateway->request('getCountries', new GetCountriesRequest(), $this->cache);

        return GetCountriesResponse::fromApiResponse($response);
    }

    /**
     * @param GetBrandsRequest $request
     *
     * @return GetBrandsResponse
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getBrands(GetBrandsRequest $request): GetBrandsResponse
    {
        $request->setArticleCountry($this->gateway->getCountryCode());

        $response = $this->gateway->request('getBrands', $request, $this->cache);

        return GetBrandsResponse::fromApiResponse($response);
    }

    /**
     * @return GetCriteriaResponse
     *
     * @throws CacheException
     * @throws GuzzleException
     * @throws TecDocApiException
     */
    public function getCriteria(): GetCriteriaResponse
    {
        $response = $this->gateway->request('getCriteria2', new GetCriteriaRequest(), $this->cache);

        return GetCriteriaResponse::fromApiResponse($response);
    }

    /**
     * @return GetGenericArticlesResponse
     *
     * @throws CacheException
     * @throws GuzzleException
     * @throws TecDocApiException
     */
    public function getGenericArticles(): GetGenericArticlesResponse
    {
        $response = $this->gateway->request('getGenericArticles', new GetCriteriaRequest(), $this->cache);

        return GetGenericArticlesResponse::fromApiResponse($response);
    }

    /**
     * @param GetManufacturersRequest $request
     *
     * @return GetManufacturersResponse
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getManufacturers(GetManufacturersRequest $request): GetManufacturersResponse
    {
        $response = $this->gateway->request('getManufacturers2', $request, $this->cache);

        return GetManufacturersResponse::fromApiResponse($response);
    }

    /**
     * @param GetModelSeriesRequest $request
     *
     * @return GetModelSeriesResponse
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getModelSeries(GetModelSeriesRequest $request): GetModelSeriesResponse
    {
        $response = $this->gateway->request('getModelSeries2', $request, $this->cache);

        return GetModelSeriesResponse::fromApiResponse($response);
    }

    /**
     * @param GetVehicleIdsByCriteriaRequest $request
     *
     * @return GetVehicleIdsByCriteriaResponse
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getVehicleIdsByCriteria(GetVehicleIdsByCriteriaRequest $request): GetVehicleIdsByCriteriaResponse
    {
        $request->setCountriesCarSelection($this->gateway->getCountryCode());

        $response = $this->gateway->request('getVehicleIdsByCriteria', $request, $this->cache);

        return GetVehicleIdsByCriteriaResponse::fromApiResponse($response);
    }

    /**
     * @param GetVehicleByIdsRequest $request
     *
     * @return GetVehicleByIdsResponse
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getVehiclesByIds(GetVehicleByIdsRequest $request): GetVehicleByIdsResponse
    {
        $request
            ->setArticleCountry($this->gateway->getCountryCode())
            ->setCountriesCarSelection($this->gateway->getCountryCode());

        $response = $this->gateway->request('getVehicleByIds4', $request, $this->cache);

        return GetVehicleByIdsResponse::fromApiResponse($response);
    }

    /**
     * @param GetLinkageTargetsRequest $request
     *
     * @return GetLinkageTargetsResponse
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getLinkageTargets(GetLinkageTargetsRequest $request): GetLinkageTargetsResponse
    {
        $request->setLinkageTargetCountry($this->gateway->getCountryCode());

        $response = $this->gateway->request('getLinkageTargets', $request, $this->cache);

        return GetLinkageTargetsResponse::fromApiResponse($response);
    }

    /**
     * This feature must be enabled on your TecDoc Account.
     *
     * @param GetVehiclesByVinRequest $request
     *
     * @return GetVehiclesByVinResponse
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getVehiclesByVin(GetVehiclesByVinRequest $request): GetVehiclesByVinResponse
    {
        $response = $this->gateway->request('getVehiclesByVIN', $request, $this->cache);

        return GetVehiclesByVinResponse::fromApiResponse($response);
    }

    /**
     * @param GetArticlesRequest $request
     *
     * @return GetArticlesResponse
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getArticles(GetArticlesRequest $request): GetArticlesResponse
    {
        $request->setArticleCountry($this->gateway->getCountryCode());

        $response = $this->gateway->request('getArticles', $request, $this->cache);

        return GetArticlesResponse::fromApiResponse($response);
    }

    /**
     * @param GetArticleLinkedManufacturersRequest $request
     *
     * @return GetArticleLinkedManufacturersResponse
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getArticleLinkedManufacturers(GetArticleLinkedManufacturersRequest $request): GetArticleLinkedManufacturersResponse
    {
        $request->setArticleCountry($this->gateway->getCountryCode());

        $response = $this->gateway->request('getArticleLinkedAllLinkingTargetManufacturer2', $request, $this->cache);

        return GetArticleLinkedManufacturersResponse::fromApiResponse($response);
    }

    /**
     * @param GetArticleLinkedAllLinkingTargetRequest $request
     *
     * @return GetArticleLinkedAllLinkingTargetResponse
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getArticleLinkages(GetArticleLinkedAllLinkingTargetRequest $request): GetArticleLinkedAllLinkingTargetResponse
    {
        $request->setArticleCountry($this->gateway->getCountryCode());

        $response = $this->gateway->request('getArticleLinkedAllLinkingTarget4', $request, $this->cache);

        return GetArticleLinkedAllLinkingTargetResponse::fromApiResponse($response);
    }

    /**
     * @param GetVehiclesByKeyNumberPlatesRequest $request
     *
     * @return GetVehiclesByKeyNumberPlatesResponse
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getVehiclesByKeyNumberPlates(GetVehiclesByKeyNumberPlatesRequest $request): GetVehiclesByKeyNumberPlatesResponse
    {
        $response = $this->gateway->request('getVehiclesByKeyNumberPlates', $request, $this->cache);

        return GetVehiclesByKeyNumberPlatesResponse::fromApiResponse($response);
    }

    /**
     * @param string $documentId
     *
     * @return string
     */
    public function getDocumentUrl(string $documentId): string
    {
        return $this->gateway->getDocumentUrl($documentId);
    }

    /**
     * @return Gateway
     */
    public function getGateway(): Gateway
    {
        return $this->gateway;
    }

    /**
     * @param bool $cache
     *
     * @return $this
     */
    public function setCache(bool $cache): static
    {
        $this->cache = $cache;

        return $this;
    }
}
