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
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Criteria;
use GuzzleHttp\Exception\GuzzleException;
use ItPremium\TecDoc\Api\Exception\TecDocApiException;
use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Enum\LinkingTargetType;
use ItPremium\TecDoc\Model\Data\GroupedModelSeries;
use ItPremium\TecDoc\Model\Data\ModelSeries;
use ItPremium\TecDoc\Repository\Api\ModelSeriesRepository;
use Symfony\Component\Cache\Exception\CacheException;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class ModelSeriesService
{
    /**
     * ModelService constructor.
     *
     * @param ModelSeriesRepository $modelRepository
     * @param ManufacturerService $manufacturerService
     */
    public function __construct(
        private readonly ModelSeriesRepository $modelRepository,
        private readonly ManufacturerService $manufacturerService,
    ) {
    }

    /**
     * @param int $manufacturerId
     * @param int $modelSeriesId
     * @param LinkingTargetType $linkingTargetType
     *
     * @return ModelSeries|bool
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getModelSeriesById(int $manufacturerId, int $modelSeriesId, LinkingTargetType $linkingTargetType = LinkingTargetType::PASSENGER): ModelSeries|bool
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('id', $modelSeriesId));

        return $this
            ->getModelSeries($manufacturerId, $linkingTargetType)
            ->matching($criteria)
            ->first();
    }

    /**
     * @param int $manufacturerId
     * @param LinkingTargetType $linkingTargetType
     *
     * @return ArrayCollection<int, ModelSeries>
     *
     * @throws MappingError
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getModelSeries(int $manufacturerId, LinkingTargetType $linkingTargetType = LinkingTargetType::PASSENGER): ArrayCollection
    {
        $modelSeries = $this
            ->modelRepository
            ->getModelSeries($manufacturerId, $linkingTargetType);

        return $this->prepareModelSeries($manufacturerId, $linkingTargetType, $modelSeries);
    }

    /**
     * @param int $manufacturerId
     * @param LinkingTargetType $linkingTargetType
     *
     * @return ArrayCollection
     *
     * @throws MappingError
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getGroupedModelSeries(int $manufacturerId, LinkingTargetType $linkingTargetType = LinkingTargetType::PASSENGER): ArrayCollection
    {
        $out = $groupNames = $groupModels = [];

        foreach ($this->getModelSeries($manufacturerId, $linkingTargetType) as $model) {
            if (count($groupNames) > 0) {
                foreach ($groupNames as $i => $groupName) {
                    if (!str_starts_with($model->name, $groupName)) {
                        if ($i === 0) {
                            $out[end($groupNames)] = $groupModels;
                            $groupNames = $groupModels = [];
                        } else {
                            $groupNames = array_slice($groupNames, 0, $i);
                        }
                        break;
                    }
                }
            }

            if (count($groupNames) === 0) {
                $longestGroupName = explode('(', $model->name, 2)[0];

                $groupNames = [
                    $longestGroupName . ' ',
                ];

                while (($lastSpacePos = strrpos($longestGroupName, ' ')) !== false) {
                    $longestGroupName = substr($longestGroupName, 0, $lastSpacePos);

                    $groupNames[] = $longestGroupName . ' ';
                }

                $groupNames = array_reverse($groupNames);
            }

            $groupModels[] = $model;
        }

        if (count($groupModels) > 0) {
            $out[end($groupNames)] = $groupModels;
        }

        $groupedModelSeries = new ArrayCollection();

        foreach ($out as $groupName => $modelSeries) {
            $modelSeries = new ArrayCollection($modelSeries);

            $criteria = Criteria::create()
                ->orderBy(['yearFrom' => 'ASC']);

            $groupedModelSeries->add(
                new GroupedModelSeries(trim($groupName), $linkingTargetType, $modelSeries->matching($criteria))
            );
        }

        return $groupedModelSeries;
    }

    public function generateYearsFilter(ArrayCollection $modelSeries): array
    {
        $decades = [];

        foreach ($modelSeries as $model) {
            $yearFrom = $model->yearFrom;
            $yearTo = $model->yearTo;

            $startDecade = (int) floor($yearFrom / 10) * 10;
            $endDecade = (int) floor($yearTo / 10) * 10;

            for ($decade = $startDecade; $decade <= $endDecade; $decade += 10) {
                $decadeStart = max($yearFrom, $decade);
                $decadeEnd = min($yearTo, $decade + 9);

                $yearsInDecade = range($decadeStart, $decadeEnd);

                if (isset($decades[$decade])) {
                    $decades[$decade]['years'] = array_unique(
                        array_merge($decades[$decade]['years'], $yearsInDecade)
                    );
                } else {
                    $decades[$decade] = [
                        'decade' => $decade,
                        'years' => $yearsInDecade,
                    ];
                }

                sort($decades[$decade]['years']);
            }
        }

        ksort($decades);

        return $decades;
    }

    /**
     * @param int $manufacturerId
     * @param LinkingTargetType $linkingTargetType
     * @param ArrayCollection<int, ModelSeries> $modelSeries
     *
     * @return ArrayCollection<int, ModelSeries>
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     */
    private function prepareModelSeries(int $manufacturerId, LinkingTargetType $linkingTargetType, ArrayCollection $modelSeries): ArrayCollection
    {
        $manufacturer = $this
            ->manufacturerService
            ->getManufacturerById($manufacturerId, $linkingTargetType);

        if (!$manufacturer->active) {
            return new ArrayCollection();
        }

        foreach ($modelSeries as $model) {
            $model->linkingTargetType = $linkingTargetType;
            $model->manufacturerId = $manufacturerId;
            $model->manufacturerName = $manufacturer->name;
        }

        $minYear = \Configuration::get(ConfigurationConstant::TECDOC_MIN_MODEL_YEAR);

        $criteria = Criteria::create()
            ->orderBy(['name' => 'ASC']);

        return $modelSeries->filter(function (ModelSeries $modelSeries) use ($minYear) {
            return !$modelSeries->yearFrom || $modelSeries->yearFrom >= $minYear;
        })->matching($criteria);
    }
}
