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

namespace ItPremium\TecDoc\Model\Data\LinkageTarget;

use Doctrine\Common\Collections\ArrayCollection;
use ItPremium\TecDoc\Enum\LinkingTargetType;
use ItPremium\TecDoc\Model\Data\ImageRecord;
use ItPremium\TecDoc\Model\Data\Trait\HasProductionYears;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class LinkageTargetDetails
{
    use HasProductionYears;

    /**
     * LinkageTargetDetails constructor.
     *
     * @param int $id
     * @param LinkingTargetType $linkingTargetType
     * @param int $manufacturerId
     * @param string $manufacturerName
     * @param ?int $modelSeriesId
     * @param ?string $modelSeriesName
     * @param ?string $description
     * @param ?int $cylinders
     * @param ?int $capacityCC
     * @param ?float $capacityLiters
     * @param ?string $fuelType
     * @param ?string $driveType
     * @param ?int $horsePowerFrom
     * @param ?int $horsePowerTo
     * @param ?int $kiloWattsFrom
     * @param ?int $kiloWattsTo
     * @param ?int $yearFrom
     * @param ?string $monthFrom
     * @param ?int $yearTo
     * @param ?string $monthTo
     * @param ArrayCollection<int, LinkageTargetEngine> $engines
     * @param ArrayCollection<int, ImageRecord> $images
     */
    public function __construct(
        /** @var int */
        public readonly int $id,

        /** @var LinkingTargetType */
        public readonly LinkingTargetType $linkingTargetType,

        /** @var int */
        public readonly int $manufacturerId,

        /** @var string */
        public readonly string $manufacturerName,

        /** @var ?int */
        public readonly ?int $modelSeriesId,

        /** @var ?string */
        public readonly ?string $modelSeriesName,

        /** @var ?string */
        public readonly ?string $description,

        /** @var ?int */
        public readonly ?int $cylinders,

        /** @var ?int */
        public readonly ?int $capacityCC,

        /** @var ?float */
        public readonly ?float $capacityLiters,

        /** @var ?string */
        public readonly ?string $fuelType,

        /** @var ?string */
        public readonly ?string $driveType,

        /** @var ?int */
        public readonly ?int $horsePowerFrom,

        /** @var ?int */
        public readonly ?int $horsePowerTo,

        /** @var ?int */
        public readonly ?int $kiloWattsFrom,

        /** @var ?int */
        public readonly ?int $kiloWattsTo,

        /** @var ?int */
        public readonly ?int $yearFrom,

        /** @var ?string */
        public readonly ?string $monthFrom,

        /** @var ?int */
        public ?int $yearTo,

        /** @var ?string */
        public ?string $monthTo,

        /** @var ArrayCollection<int, LinkageTargetEngine> */
        public readonly ArrayCollection $engines,

        /** @var ArrayCollection<int, ImageRecord> */
        public readonly ArrayCollection $images,
    ) {
        $this->initTrait();
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        if (!$this->images->isEmpty()) {
            return $this->images->first()->getImageUrl();
        }

        return _MODULE_DIR_ . 'itp_tecdoc/views/img/models/' . $this->linkingTargetType->slug() . '/no-image.jpg';
    }

    /**
     * @return string
     */
    public function getManufacturerSlug(): string
    {
        return \Tools::str2url($this->manufacturerName);
    }

    /**
     * @return string
     */
    public function getModelSeriesSlug(): string
    {
        return \Tools::str2url($this->modelSeriesName);
    }

    /**
     * @return string
     */
    public function getVehicleSlug(): string
    {
        return \Tools::str2url($this->description);
    }

    /**
     * @param ?int $langId
     *
     * @return string
     *
     * @throws \PrestaShopException
     */
    public function getLink(?int $langId = null): string
    {
        if (!$langId) {
            $langId = \Context::getContext()->language->id;
        }

        $dispatcher = \Dispatcher::getInstance();

        $params = [
            'linking_target_type_slug' => $this->linkingTargetType->slug(),
            'vehicle_id' => $this->id,
        ];

        if ($dispatcher->hasKeyword('module-itp_tecdoc-assemblyGroups', $langId, 'manufacturer_slug')) {
            $params['manufacturer_slug'] = $this->getManufacturerSlug();
        }

        if ($dispatcher->hasKeyword('module-itp_tecdoc-assemblyGroups', $langId, 'model_series_slug')) {
            $params['model_series_slug'] = $this->getModelSeriesSlug();
        }

        if ($dispatcher->hasKeyword('module-itp_tecdoc-assemblyGroups', $langId, 'vehicle_slug')) {
            $params['vehicle_slug'] = $this->getVehicleSlug();
        }

        return \Context::getContext()->link->getModuleLink('itp_tecdoc', 'assemblyGroups', $params, true, $langId);
    }
}
