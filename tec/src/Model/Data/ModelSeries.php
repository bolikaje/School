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

use ItPremium\TecDoc\Enum\LinkingTargetType;
use ItPremium\TecDoc\Model\Data\Trait\HasProductionYears;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class ModelSeries
{
    use HasProductionYears;

    /**
     * ModelSeries constructor.
     *
     * @param int $id
     * @param ?int $manufacturerId
     * @param ?string $manufacturerName
     * @param ?LinkingTargetType $linkingTargetType
     * @param string $name
     * @param ?int $yearFrom
     * @param ?string $monthFrom
     * @param ?int $yearTo
     * @param ?string $monthTo
     */
    public function __construct(
        /** @var int */
        public readonly int $id,

        /** @var ?int */
        public ?int $manufacturerId,

        /** @var ?string */
        public ?string $manufacturerName,

        /** @var ?LinkingTargetType */
        public ?LinkingTargetType $linkingTargetType,

        /** @var string */
        public readonly string $name,

        /** @var ?int */
        public readonly ?int $yearFrom,

        /** @var ?string */
        public readonly ?string $monthFrom,

        /** @var ?int */
        public ?int $yearTo,

        /** @var ?string */
        public ?string $monthTo,
    ) {
        $this->initTrait();
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
        return \Tools::str2url($this->name);
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
            'manufacturer_slug' => $this->getManufacturerSlug(),
            'model_series_id' => $this->id,
        ];

        if ($dispatcher->hasKeyword('module-itp_tecdoc-vehicles', $langId, 'model_series_slug')) {
            $params['model_series_slug'] = $this->getModelSeriesSlug();
        }

        return \Context::getContext()->link->getModuleLink('itp_tecdoc', 'vehicles', $params, true, $langId);
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        $noImagePath = _MODULE_DIR_ . 'itp_tecdoc/views/img/models/no-image.jpg';

        if (!$this->linkingTargetType) {
            return $noImagePath;
        }

        $directory = 'itp_tecdoc/views/img/models/' . $this->linkingTargetType->slug() . '/';

        if (file_exists(_PS_MODULE_DIR_ . $directory . $this->id . '.jpg')) {
            return _MODULE_DIR_ . $directory . $this->id . '.jpg';
        }

        return $noImagePath;
    }
}
