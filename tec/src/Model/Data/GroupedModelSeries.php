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

namespace ItPremium\TecDoc\Model\Data;

use Doctrine\Common\Collections\ArrayCollection;
use ItPremium\TecDoc\Enum\LinkingTargetType;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class GroupedModelSeries
{
    /**
     * GroupedModelSeries constructor.
     *
     * @param string $name
     * @param LinkingTargetType $linkingTargetType
     * @param ArrayCollection<int, ModelSeries> $modelSeries
     */
    public function __construct(
        public readonly string $name,
        public readonly LinkingTargetType $linkingTargetType,
        public readonly ArrayCollection $modelSeries,
    ) {
    }

    /**
     * @return int[]
     */
    public function getYears(): array
    {
        $years = [];

        foreach ($this->modelSeries as $modelSeries) {
            $yearTo = $modelSeries->yearTo;
            $yearFrom = $modelSeries->yearFrom ?? $yearTo;

            if ($yearFrom and $yearTo) {
                for ($year = $yearFrom; $year <= $yearTo; ++$year) {
                    $years[$year] = $year;
                }
            }
        }

        sort($years);

        return $years;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->modelSeries->count();
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        $modelSeries = $this->modelSeries->filter(function (ModelSeries $modelSeries) {
            return !str_ends_with($modelSeries->getImage(), 'no-image.jpg');
        })->last();

        if ($modelSeries) {
            return $modelSeries->getImage();
        }

        return _MODULE_DIR_ . 'itp_tecdoc/views/img/models/no-image.jpg';
    }
}
