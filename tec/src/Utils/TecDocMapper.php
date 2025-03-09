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

namespace ItPremium\TecDoc\Utils;

use CuyZ\Valinor\Mapper\MappingError;
use CuyZ\Valinor\Mapper\Source\Source;
use CuyZ\Valinor\MapperBuilder;
use Doctrine\Common\Collections\ArrayCollection;
use ItPremium\TecDoc\Enum\CriteriaType;
use ItPremium\TecDoc\Enum\LinkingTargetType;
use ItPremium\TecDoc\Model\Data\Article\Article;
use ItPremium\TecDoc\Model\Data\Article\GenericArticle as ArticleGenericArticle;
use ItPremium\TecDoc\Model\Data\AssemblyGroup;
use ItPremium\TecDoc\Model\Data\Brand;
use ItPremium\TecDoc\Model\Data\Country;
use ItPremium\TecDoc\Model\Data\Criteria\Criteria as ArticleCriteria;
use ItPremium\TecDoc\Model\Data\GenericArticle;
use ItPremium\TecDoc\Model\Data\ImageRecord;
use ItPremium\TecDoc\Model\Data\Language;
use ItPremium\TecDoc\Model\Data\LinkageTarget\LinkageTargetDetails;
use ItPremium\TecDoc\Model\Data\Manufacturer;
use ItPremium\TecDoc\Model\Data\ModelSeries;
use ItPremium\TecDoc\Model\Data\Vehicle;
use ItPremium\TecDoc\Model\Data\VehicleByKeyNumberPlates;
use ItPremium\TecDoc\Utils\Source\TecDocSource;

if (!defined('_PS_VERSION_')) {
    exit;
}

class TecDocMapper
{
    /**
     * TecDocMapper constructor.
     *
     * @param MapperBuilder $mapperBuilder
     */
    public function __construct(
        private readonly MapperBuilder $mapperBuilder,
    ) {
    }

    /**
     * This method was developed as a more efficient alternative to the mapArticles method.
     *
     * @param array $articles
     *
     * @return ArrayCollection<int, Article>
     */
    public function mapArticlesSimple(array $articles): ArrayCollection
    {
        $mappedArticles = new ArrayCollection();

        foreach ($articles as $article) {
            $criteria = new ArrayCollection();

            foreach ($article['articleCriteria'] as $articleCriteria) {
                $criteria->add(
                    new ArticleCriteria(
                        $articleCriteria['criteriaId'],
                        $articleCriteria['criteriaDescription'],
                        CriteriaType::from($articleCriteria['criteriaType']),
                        $articleCriteria['rawValue'],
                        $articleCriteria['formattedValue'],
                        $articleCriteria['isMandatory'],
                        $articleCriteria['isInterval'],
                    )
                );
            }

            $genericArticles = new ArrayCollection();

            $genericArticles->add(
                new ArticleGenericArticle(
                    $article['genericArticles'][0]['genericArticleId'],
                    $article['genericArticles'][0]['genericArticleDescription'],
                    $article['genericArticles'][0]['legacyArticleId']
                )
            );

            $images = new ArrayCollection();

            if (!empty($article['images'])) {
                $images->add(
                    new ImageRecord(
                        $article['images'][0]['imageURL50'],
                        $article['images'][0]['imageURL100'],
                        $article['images'][0]['imageURL200'],
                        $article['images'][0]['imageURL400'],
                        $article['images'][0]['imageURL1600'],
                        $article['images'][0]['imageURL800'],
                    )
                );
            }

            $mappedArticles->add(
                new Article(
                    brandId: $article['dataSupplierId'],
                    brandName: $article['mfrName'],
                    reference: $article['articleNumber'],
                    criteria: $criteria,
                    genericArticles: $genericArticles,
                    images: $images
                )
            );
        }

        return $mappedArticles;
    }

    /**
     * @param array $articles
     *
     * @return ArrayCollection<int, Article>
     *
     * @throws MappingError
     */
    public function mapArticles(array $articles): ArrayCollection
    {
        $source = Source::array($articles)->map([
            '*.articleCriteria' => 'criteria',
            '*.articleCriteria.*.criteriaDescription' => 'description',
            '*.articleCriteria.*.criteriaId' => 'id',
            '*.articleCriteria.*.criteriaType' => 'type',
            '*.articleNumber' => 'reference',
            '*.genericArticles.*.genericArticleDescription' => 'description',
            '*.genericArticles.*.genericArticleId' => 'id',
            '*.dataSupplierId' => 'brandId',
            '*.mfrName' => 'brandName',
            '*.oemNumbers.*.mfrName' => 'manufacturerName',
            '*.replacedByArticles' => 'replacements',
            '*.replacedByArticles.*.dataSupplierId' => 'brandId',
            '*.replacedByArticles.*.mfrName' => 'brandName',
        ]);

        return $this->map(ArrayCollection::class . '<int, ' . Article::class . '>', $source);
    }

    /**
     * @param array $assemblyGroups
     *
     * @return ArrayCollection<int, AssemblyGroup>
     *
     * @throws MappingError
     */
    public function mapAssemblyGroups(array $assemblyGroups): ArrayCollection
    {
        $source = Source::array($assemblyGroups)->map([
            '*.assemblyGroupNodeId' => 'id',
            '*.assemblyGroupName' => 'name',
            '*.assemblyGroupType' => 'type',
        ]);

        return $this->map(ArrayCollection::class . '<int, ' . AssemblyGroup::class . '>', $source);
    }

    /**
     * @param array $brands
     *
     * @return ArrayCollection<int, Brand>
     *
     * @throws MappingError
     */
    public function mapBrands(array $brands): ArrayCollection
    {
        foreach ($brands as &$brand) {
            $brand['addressDetails'] = $brand['addressDetails'][0] ?? null;
        }

        $source = Source::array($brands)->map([
            '*.dataSupplierId' => 'id',
            '*.dataSupplierLogo' => 'image',
            '*.mfrName' => 'name',
            '*.addressDetails' => 'address',
            '*.addressDetails.street' => 'address',
            '*.addressDetails.wwwURL' => 'site',
            '*.addressDetails.zipCountryCodeISO' => 'country',
        ]);

        return $this->map(ArrayCollection::class . '<int, ' . Brand::class . '>', $source);
    }

    /**
     * @param array $countries
     *
     * @return ArrayCollection<int, Country>
     *
     * @throws MappingError
     */
    public function mapCountries(array $countries): ArrayCollection
    {
        $source = Source::array($countries)->map([
            '*.countryCode' => 'code',
            '*.countryName' => 'name',
        ]);

        return $this->map(ArrayCollection::class . '<int, ' . Country::class . '>', $source);
    }

    /**
     * @param array $genericArticles
     *
     * @return ArrayCollection<int, GenericArticle>
     *
     * @throws MappingError
     */
    public function mapGenericArticles(array $genericArticles): ArrayCollection
    {
        $mappedGenericArticles = new ArrayCollection();

        foreach ($genericArticles as $genericArticle) {
            $mappedGenericArticles->add(
                new GenericArticle(
                    $genericArticle['genericArticleId'],
                    $genericArticle['assemblyGroup'] ?? null,
                    $genericArticle['designation'] ?? null,
                    $genericArticle['masterDesignation'] ?? null,
                    $genericArticle['usageDesignation'] ?? null,
                )
            );
        }

        return $mappedGenericArticles;
    }

    /**
     * @param array $languages
     *
     * @return ArrayCollection<int, Language>
     *
     * @throws MappingError
     */
    public function mapLanguages(array $languages): ArrayCollection
    {
        $source = Source::array($languages)->map([
            '*.languageCode' => 'code',
            '*.languageName' => 'name',
        ]);

        return $this->map(ArrayCollection::class . '<int, ' . Language::class . '>', $source);
    }

    /**
     * @param array $manufacturers
     *
     * @return ArrayCollection<int, Manufacturer>
     *
     * @throws MappingError
     */
    public function mapManufacturers(array $manufacturers): ArrayCollection
    {
        $source = Source::array($manufacturers)->map([
            '*.manuId' => 'id',
            '*.manuName' => 'name',
        ]);

        return $this->map(ArrayCollection::class . '<int, ' . Manufacturer::class . '>', $source);
    }

    /**
     * @param array $modelSeries
     *
     * @return ArrayCollection<int, ModelSeries>
     *
     * @throws MappingError
     */
    public function mapModelSeries(array $modelSeries): ArrayCollection
    {
        foreach ($modelSeries as &$model) {
            if (isset($model['yearOfConstrFrom'])) {
                $model['monthFrom'] = Helper::extractMonthFromTecDocDate($model['yearOfConstrFrom']);
            }

            if (isset($model['yearOfConstrTo'])) {
                $model['monthTo'] = Helper::extractMonthFromTecDocDate($model['yearOfConstrTo']);
            }
        }

        $source = Source::array($modelSeries)->map([
            '*.modelId' => 'id',
            '*.modelname' => 'name',
            '*.yearOfConstrFrom' => 'yearFrom',
            '*.yearOfConstrTo' => 'yearTo',
        ]);

        return $this->map(ArrayCollection::class . '<int, ' . ModelSeries::class . '>', $source);
    }

    /**
     * @param array $vehicles
     *
     * @return ArrayCollection<int, Vehicle>
     *
     * @throws MappingError
     */
    public function mapVehicles(array $vehicles): ArrayCollection
    {
        foreach ($vehicles as &$vehicle) {
            $vehicle['motorCodes'] = $vehicle['motorCodes']['array'] ?? null;

            if (isset($vehicle['vehicleDetails']['yearOfConstrFrom'])) {
                $vehicle['vehicleDetails']['monthFrom'] = Helper::extractMonthFromTecDocDate($vehicle['vehicleDetails']['yearOfConstrFrom']);
            }

            if (isset($vehicle['vehicleDetails']['yearOfConstrTo'])) {
                $vehicle['vehicleDetails']['monthTo'] = Helper::extractMonthFromTecDocDate($vehicle['vehicleDetails']['yearOfConstrTo']);
            }

            if (!isset($vehicle['vehicleDetails'])) {
                unset($vehicle);
            }
        }

        $source = Source::array($vehicles)->map([
            '*.carId' => 'id',
            '*.vehicleDocId' => 'documentId',
            '*.vehicleDetails' => 'details',
            '*.vehicleDetails.impulsionType' => 'drive',
            '*.vehicleDetails.manuId' => 'manufacturerId',
            '*.vehicleDetails.manuName' => 'manufacturerName',
            '*.vehicleDetails.modId' => 'modelId',
            '*.vehicleDetails.modName' => 'modelName',
            '*.vehicleDetails.powerHpTo' => 'powerHp',
            '*.vehicleDetails.powerKwTo' => 'powerKw',
            '*.vehicleDetails.yearOfConstrFrom' => 'yearFrom',
            '*.vehicleDetails.yearOfConstrTo' => 'yearTo',
        ]);

        return $this->map(ArrayCollection::class . '<int, ' . Vehicle::class . '>', $source);
    }

    /**
     * @param array $linkageTargets
     *
     * @return ArrayCollection<int, LinkageTargetDetails>
     *
     * @throws MappingError
     */
    public function mapLinkageTargets(array $linkageTargets): ArrayCollection
    {
        /*
         * While motorcycles are considered passenger vehicles, we're providing a dedicated navigation for them.
         * As a result, we need to exceptionally differentiate motorcycles from the subLinkageTarget.
         */
        foreach ($linkageTargets as &$linkageTarget) {
            if (isset($linkageTarget['subLinkageTargetType']) and LinkingTargetType::tryFrom($linkageTarget['subLinkageTargetType']) == LinkingTargetType::MOTORCYCLE) {
                $linkageTarget['linkageTargetType'] = $linkageTarget['subLinkageTargetType'];
            }

            if (isset($linkageTarget['beginYearMonth'])) {
                $linkageTarget['monthFrom'] = Helper::extractMonthFromTecDocDate($linkageTarget['beginYearMonth'], false);
            }

            if (isset($linkageTarget['endYearMonth'])) {
                $linkageTarget['monthTo'] = Helper::extractMonthFromTecDocDate($linkageTarget['endYearMonth'], false);
            }

            unset($linkageTarget['subLinkageTargetType']);
        }

        $source = Source::array($linkageTargets)->map([
            '*.beginYearMonth' => 'yearFrom',
            '*.endYearMonth' => 'yearTo',
            '*.linkageTargetId' => 'id',
            '*.linkageTargetType' => 'linkingTargetType',
            '*.mfrId' => 'manufacturerId',
            '*.mfrName' => 'manufacturerName',
            '*.vehicleImages' => 'images',
            '*.vehicleModelSeriesId' => 'modelSeriesId',
            '*.vehicleModelSeriesName' => 'modelSeriesName',
        ]);

        return $this->map(ArrayCollection::class . '<int, ' . LinkageTargetDetails::class . '>', $source);
    }

    /**
     * @param array $vehicles
     *
     * @return ArrayCollection<int, VehicleByKeyNumberPlates>
     *
     * @throws MappingError
     */
    public function mapVehiclesByKeyNumberPlates(array $vehicles): ArrayCollection
    {
        $source = Source::array($vehicles)->map([
            '*.carId' => 'id',
            '*.carName' => 'name',
            '*.manuId' => 'manufacturerId',
            '*.modelId' => 'modelSeriesId',
        ]);

        return $this->map(ArrayCollection::class . '<int, ' . VehicleByKeyNumberPlates::class . '>', $source);
    }

    /**
     * Map source TecDoc array to model object
     *
     * @param $signature
     * @param $source
     *
     * @return mixed
     *
     * @throws MappingError
     */
    private function map($signature, $source): mixed
    {
        $source = Source::iterable(
            new TecDocSource($source)
        );

        return $this
            ->mapperBuilder
            ->enableFlexibleCasting()
            ->allowSuperfluousKeys()
            ->mapper()
            ->map($signature, $source);
    }
}
