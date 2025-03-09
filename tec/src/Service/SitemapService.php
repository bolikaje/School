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
use Doctrine\ORM\EntityManagerInterface;
use GuzzleHttp\Exception\GuzzleException;
use ItPremium\TecDoc\Api\Exception\TecDocApiException;
use ItPremium\TecDoc\Api\TecDocApi;
use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Enum\LinkingTargetType;
use ItPremium\TecDoc\Model\Query\GetLinkageTargetsQuery;
use ItPremium\TecDoc\Repository\StockRepository;
use ItPremium\TecDoc\Utils\Helper;
use Symfony\Component\Cache\Exception\CacheException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class SitemapService
{
    private const FEED_FILE = 'feed.csv';
    private const SITEMAP_FILE = 'itp_tecdoc_sitemap.xml';
    private const SITEMAP_LINKS_LIMIT = 50000;

    private const MANUFACTURER_LABEL = 'MANUFACTURER';
    private const MODEL_LABEL = 'MODEL';
    private const VEHICLE_LABEL = 'VEHICLE';
    private const ASSEMBLY_GROUP_LABEL = 'ASSEMBLY_GROUP';
    private const ARTICLE_LABEL = 'ARTICLE';

    /**
     * @var array
     */
    private array $links = [];

    /**
     * @var array
     */
    private array $sitemapLinks = [];

    /**
     * @var int
     */
    private int $index = 0;

    /**
     * AssemblyGroupService constructor.
     *
     * @param EntityManagerInterface $entityManager
     * @param TecDocApi $tecDocApi
     * @param StockRepository $stockRepository
     * @param ArticleService $articleService
     * @param AssemblyGroupService $assemblyGroupService
     * @param ManufacturerService $manufacturerService
     * @param ModelSeriesService $modelSeriesService
     * @param VehicleService $vehicleService
     * @param Helper $helper
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly TecDocApi $tecDocApi,
        private readonly StockRepository $stockRepository,
        private readonly ArticleService $articleService,
        private readonly AssemblyGroupService $assemblyGroupService,
        private readonly ManufacturerService $manufacturerService,
        private readonly ModelSeriesService $modelSeriesService,
        private readonly VehicleService $vehicleService,
        private readonly Helper $helper,
    ) {
    }

    /**
     * @param \Language $language
     * @param int $shopId
     *
     * @return string
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws CacheException
     * @throws \PrestaShopException
     */
    public function generateIndexSitemap(\Language $language, int $shopId): string
    {
        $this->generateSitemaps($language, $shopId);

        $tecdocSitemapPath = $this->getFilePath($shopId . '_' . $language->iso_code . '_' . self::SITEMAP_FILE);

        $tecdocSitemap = new \SimpleXMLElement('<sitemapindex></sitemapindex>');
        $tecdocSitemap->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($this->sitemapLinks as $sitemapLink) {
            $sitemap = $tecdocSitemap->addChild('sitemap');
            $sitemap->addChild('loc', $sitemapLink);
            $sitemap->addChild('lastmod', date('c'));
        }

        $tecdocSitemap->asXML($tecdocSitemapPath);

        return $tecdocSitemapPath;
    }

    /**
     * @param \Language $language
     * @param int $shopId
     *
     * @return array
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws CacheException
     * @throws \PrestaShopException
     */
    public function generateSitemaps(\Language $language, int $shopId): array
    {
        $this->deleteData($language, $shopId);
        $this->generateFeed($language, $shopId);

        $this->sitemapLinks = [];
        $this->index = $row = 0;

        if ($import = fopen($this->getFilePath($shopId . '_' . $language->iso_code . '_' . self::FEED_FILE), 'r')) {
            while ($data = fgetcsv($import)) {
                ++$row;

                $this->links[] = $data[0];

                if ($row % self::SITEMAP_LINKS_LIMIT == 0) {
                    $this->createSitemapFile($language, $shopId);
                }
            }

            if (!empty($this->links)) {
                $this->createSitemapFile($language, $shopId);
            }
        }

        return $this->sitemapLinks;
    }

    /**
     * @param \Language $language
     * @param int $shopId
     *
     * @return void
     */
    private function deleteData(\Language $language, int $shopId): void
    {
        $filesystem = new Filesystem();

        $finder = new Finder();

        $finder
            ->in($this->getFilePath())
            ->name([$shopId . '_' . $language->iso_code . '*']);

        foreach ($finder as $file) {
            $filesystem->remove($file->getRealPath());
        }
    }

    /**
     * @param \Language $language
     * @param int $shopId
     *
     * @return void
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws CacheException
     * @throws \PrestaShopException
     */
    private function generateFeed(\Language $language, int $shopId): void
    {
        /*
         * Change language context for API queries.
         */
        $this
            ->tecDocApi
            ->getGateway()
            ->setLanguageCode(
                $this->helper->getCurrentTecDocLanguage($language->iso_code)
            );

        $this->tecDocApi->setCache(true);

        $feedFile = fopen($this->getFilePath($shopId . '_' . $language->iso_code . '_' . self::FEED_FILE), 'wb');

        if (!$feedFile) {
            return;
        }

        $linkingTargetTypes = LinkingTargetType::getAccessibleLinkingTargetTypes();

        foreach ($linkingTargetTypes as $linkingTargetType) {
            $manufacturers = $this
                ->manufacturerService
                ->getManufacturers(linkingTargetType: $linkingTargetType);

            foreach ($manufacturers as $manufacturer) {
                fputcsv($feedFile, [$manufacturer->getLink($linkingTargetType, $language->id), self::MANUFACTURER_LABEL]);

                $modelSeries = $this
                    ->modelSeriesService
                    ->getModelSeries($manufacturer->id, $linkingTargetType);

                foreach ($modelSeries as $model) {
                    fputcsv($feedFile, [$model->getLink($language->id), self::MODEL_LABEL]);

                    $getLinkageTargetsQuery = (new GetLinkageTargetsQuery())
                        ->setLinkageTargetType($linkingTargetType->value)
                        ->setVehicleModelSeriesIds($model->id);

                    $vehicles = $this
                        ->vehicleService
                        ->getLinkageTargets($getLinkageTargetsQuery);

                    foreach ($vehicles as $vehicle) {
                        fputcsv($feedFile, [$vehicle->getLink($language->id), self::VEHICLE_LABEL]);

                        $assemblyGroups = $this
                            ->assemblyGroupService
                            ->getAssemblyGroups($vehicle->id, $linkingTargetType);

                        foreach ($assemblyGroups as $assemblyGroup) {
                            fputcsv($feedFile, [$assemblyGroup->getLink($vehicle, $language->id), self::ASSEMBLY_GROUP_LABEL]);
                        }
                    }
                }
            }
        }

        /*
         * Currently, the sitemap for articles is generated only for existing stock records that may also be custom articles.
         * The reason for this is that we can't get the full list of articles from TecDoc as we are limited to only 10,000 results.
         * If there is a TecDoc article record for the stock record, a 301 redirect will occur to the appropriate page.
         */
        if (\Configuration::get(ConfigurationConstant::TECDOC_SHOW_CUSTOM_ARTICLES)) {
            $tecdocStocks = $this
                ->stockRepository
                ->getUniqueStockRecords();

            foreach ($tecdocStocks as $tecdocStock) {
                $article = $this
                    ->articleService
                    ->createCustomArticleFromStock($tecdocStock);

                fputcsv($feedFile, [$article->getLink($language->id), self::ARTICLE_LABEL]);

                $this->entityManager->detach($tecdocStock);
            }
        }

        fclose($feedFile);
    }

    /**
     * @param \Language $language
     * @param int $shopId
     *
     * @return void
     */
    private function createSitemapFile(\Language $language, int $shopId): void
    {
        ++$this->index;

        $tecdocSitemapPath = $this->getFilePath($shopId . '_' . $language->iso_code . '_' . $this->index . '_' . self::SITEMAP_FILE);

        $tecdocSitemap = new \SimpleXMLElement('<urlset></urlset>');
        $tecdocSitemap->addAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach ($this->links as $link) {
            $sitemap = $tecdocSitemap->addChild('url');
            $sitemap->addChild('loc', $link);
            $sitemap->addChild('lastmod', date('c'));
            $sitemap->addChild('changefreq', 'weekly');
            $sitemap->addChild('priority', '0.9');
        }

        $tecdocSitemap->asXML($tecdocSitemapPath);

        $this->links = [];
        $this->sitemapLinks[] = (\Configuration::get('PS_SSL_ENABLED')
                ? _PS_BASE_URL_SSL_
                : _PS_BASE_URL_
        ) . __PS_BASE_URI__ . 'modules/itp_tecdoc/sitemaps/' . basename($tecdocSitemapPath);
    }

    /**
     * @param string $file
     *
     * @return string
     */
    private function getFilePath(string $file = ''): string
    {
        return _PS_MODULE_DIR_ . 'itp_tecdoc' . DIRECTORY_SEPARATOR . 'sitemaps' . DIRECTORY_SEPARATOR . $file;
    }
}
