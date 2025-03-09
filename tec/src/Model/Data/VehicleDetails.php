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

use ItPremium\TecDoc\Model\Data\Trait\HasProductionYears;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class VehicleDetails
{
    use HasProductionYears;

    /**
     * VehicleDetails constructor.
     *
     * @param ?int $manufacturerId
     * @param ?string $manufacturerName
     * @param ?int $modelId
     * @param ?string $modelName
     * @param ?string $typeName
     * @param ?string $constructionType
     * @param ?int $cylinder
     * @param ?int $cylinderCapacityCcm
     * @param ?float $cylinderCapacityLiter
     * @param ?string $fuelType
     * @param ?string $drive
     * @param ?int $powerKw
     * @param ?int $powerHp
     * @param ?int $yearFrom
     * @param ?string $monthFrom
     * @param ?int $yearTo
     * @param ?string $monthTo
     * @param ?string $yearsLabel
     */
    public function __construct(
        /** @var ?int */
        public readonly ?int $manufacturerId,

        /** @var ?string */
        public readonly ?string $manufacturerName,

        /** @var ?int */
        public readonly ?int $modelId,

        /** @var ?string */
        public readonly ?string $modelName,

        /** @var ?string */
        public readonly ?string $typeName,

        /** @var ?string */
        public readonly ?string $constructionType,

        /** @var ?int */
        public readonly ?int $cylinder,

        /** @var ?int */
        public readonly ?int $cylinderCapacityCcm,

        /** @var ?float */
        public readonly ?float $cylinderCapacityLiter,

        /** @var ?string */
        public readonly ?string $fuelType,

        /** @var ?string */
        public readonly ?string $drive,

        /** @var ?int */
        public readonly ?int $powerKw,

        /** @var ?int */
        public readonly ?int $powerHp,

        /** @var ?int */
        public readonly ?int $yearFrom,

        /** @var ?string */
        public readonly ?string $monthFrom,

        /** @var ?int */
        public ?int $yearTo,

        /** @var ?string */
        public ?string $monthTo,

        /** @var ?string */
        public ?string $yearsLabel,
    ) {
        $this->initTrait();

        $this->yearsLabel = $this->getYearsLabel();
    }
}
