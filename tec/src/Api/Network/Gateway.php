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

namespace ItPremium\TecDoc\Api\Network;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\HandlerStack;
use ItPremium\TecDoc\Api\Exception\TecDocApiException;
use ItPremium\TecDoc\Api\Network\Cache\CacheMiddleware;
use ItPremium\TecDoc\Api\Request\AbstractTecDocRequest;
use Kevinrob\GuzzleCache\Storage\Psr6CacheStorage;
use Kevinrob\GuzzleCache\Strategy\GreedyCacheStrategy;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Cache\Exception\CacheException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Gateway
{
    private const CACHE_TTL = 21600;
    private const BASE_URL = 'https://webservice.tecalliance.services';
    private const END_POINT = '/pegasus-3-0/services/TecdocToCatDLB.jsonEndpoint';
    private const DOCUMENTS_URL = 'https://webservice.tecalliance.services/pegasus-3-0/documents';

    /**
     * Gateway constructor.
     *
     * @param string $provider
     * @param string $apiKey
     * @param string $countryCode
     * @param string $languageCode
     */
    public function __construct(
        private readonly string $provider,
        private readonly string $apiKey,
        private readonly string $countryCode,
        private string $languageCode,
    ) {
    }

    /**
     * @param bool $cache
     *
     * @return Client
     *
     * @throws CacheException
     */
    private function getClient(bool $cache = false): Client
    {
        $stack = HandlerStack::create();

        if ($cache) {
            $cacheMiddleware = new CacheMiddleware(
                new GreedyCacheStrategy(
                    new Psr6CacheStorage(
                        new FilesystemAdapter('cache', self::CACHE_TTL, _PS_MODULE_DIR_ . 'itp_tecdoc')
                    ),
                    self::CACHE_TTL,
                )
            );

            $cacheMiddleware->setHttpMethods(['GET' => true, 'POST' => true]);

            $stack->push($cacheMiddleware, 'cache');
        }

        return new Client([
            'base_uri' => self::BASE_URL,
            'verify' => false,
            'handler' => $stack,
        ]);
    }

    /**
     * @param string $function
     * @param AbstractTecDocRequest $request
     * @param bool $cache
     *
     * @return array
     *
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function request(string $function, AbstractTecDocRequest $request, bool $cache = false): array
    {
        $request
            ->setProvider($this->provider)
            ->setCountry($this->countryCode)
            ->setLang($this->languageCode);

        $body = json_encode([$function => $request]);

        $options = [
            'headers' => ['Content-type' => 'application/json'],
            'query' => [
                'api_key' => $this->apiKey,
            ],
            'body' => $body,
        ];

        /*
         * Since our caching package does not consider the POST body, we'll provide a
         * hash with our POST request to address this issue as workaround.
         *
         * Caching POST requests is not recommended by PSR standards. However, entire API
         * operates using POST requests, so we need to offer an option for caching them.
         */
        if ($cache) {
            $options['query']['hash'] = hash('sha256', $body);
        }

        $response = $this
            ->getClient($cache)
            ->post(self::END_POINT, $options);

        $content = json_decode($response->getBody()->getContents(), true);

        if (_PS_MODE_DEV_) {
            if ($response->getStatusCode() != 200 or (isset($content['status']) and $content['status'] != 200)) {
                throw new TecDocApiException($content['statusText']);
            }
        }

        return $content;
    }

    /**
     * @param string $documentId
     *
     * @return string
     */
    public function getDocumentUrl(string $documentId): string
    {
        return self::DOCUMENTS_URL . '/' . $this->provider . '/' . $documentId . '?api_key=' . $this->apiKey;
    }

    /**
     * @return string
     */
    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    /**
     * @param string $languageCode
     *
     * @return $this
     */
    public function setLanguageCode(string $languageCode): static
    {
        $this->languageCode = $languageCode;

        return $this;
    }
}
