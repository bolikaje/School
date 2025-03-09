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
use ItPremium\TecDoc\Enum\AssemblyGroupType;
use ItPremium\TecDoc\Model\Data\LinkageTarget\LinkageTargetDetails;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class AssemblyGroup
{
    /**
     * AssemblyGroup constructor.
     *
     * @param int $id
     * @param string $name
     * @param ?AssemblyGroupType $type
     * @param ?int $parentNodeId
     * @param int $count
     * @param ?int $depth
     */
    public function __construct(
        /** @var int */
        public readonly int $id,

        /** @var string */
        public string $name,

        public ?AssemblyGroupType $type,

        /** @var ?int */
        public readonly ?int $parentNodeId,

        /** @var int */
        public readonly int $count,

        /** @var ?int */
        public ?int $depth,
    ) {
        $this->subgroups = new ArrayCollection();
        $this->sortedSubgroups = new ArrayCollection();
        $this->unsortedSubgroups = new ArrayCollection();
    }

    /** @var ArrayCollection<int, AssemblyGroup> */
    public ArrayCollection $subgroups;

    /** @var ArrayCollection<int, AssemblyGroup> */
    public ArrayCollection $sortedSubgroups;

    /** @var ArrayCollection<int, AssemblyGroup> */
    public ArrayCollection $unsortedSubgroups;

    /**
     * @return string
     */
    public function getAssemblyGroupSlug(): string
    {
        return \Tools::str2url($this->name);
    }

    /**
     * @param ?LinkageTargetDetails $vehicle
     * @param ?int $langId
     *
     * @return string
     *
     * @throws \PrestaShopException
     */
    public function getLink(?LinkageTargetDetails $vehicle = null, ?int $langId = null): string
    {
        if (!$langId) {
            $langId = \Context::getContext()->language->id;
        }

        $params = [
            'assembly_group_id' => $this->id,
        ];

        $dispatcher = \Dispatcher::getInstance();

        if ($vehicle) {
            $params['linking_target_type_slug'] = $vehicle->linkingTargetType->slug();
            $params['vehicle_id'] = $vehicle->id;

            if ($dispatcher->hasKeyword('module-itp_tecdoc-articles', $langId, 'assembly_group_slug')) {
                $params['assembly_group_slug'] = $this->getAssemblyGroupSlug();
            }

            if ($dispatcher->hasKeyword('module-itp_tecdoc-articles', $langId, 'manufacturer_slug')) {
                $params['manufacturer_slug'] = \Tools::str2url($vehicle->manufacturerName);
            }

            if ($dispatcher->hasKeyword('module-itp_tecdoc-articles', $langId, 'model_series_slug')) {
                $params['model_series_slug'] = \Tools::str2url($vehicle->modelSeriesName);
            }

            if ($dispatcher->hasKeyword('module-itp_tecdoc-articles', $langId, 'vehicle_slug')) {
                $params['vehicle_slug'] = $vehicle->getVehicleSlug();
            }

            $link = \Context::getContext()->link->getModuleLink('itp_tecdoc', 'articles', $params, true, $langId);
        } else {
            if ($dispatcher->hasKeyword('module-itp_tecdoc-assemblyGroup', $langId, 'assembly_group_slug')) {
                $params['assembly_group_slug'] = $this->getAssemblyGroupSlug();
            }

            $link = \Context::getContext()->link->getModuleLink('itp_tecdoc', 'assemblyGroup', $params, true, $langId);
        }

        return $link;
    }

    /**
     * @param bool $fallbackImage
     *
     * @return string|bool
     */
    public function getImage(bool $fallbackImage = true): string|bool
    {
        $directory = 'itp_tecdoc/views/img/groups/';

        if (file_exists(_PS_MODULE_DIR_ . $directory . $this->id . '.jpg')) {
            return _MODULE_DIR_ . $directory . $this->id . '.jpg';
        }

        return $fallbackImage ? _MODULE_DIR_ . $directory . 'no-image.jpg' : false;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'type' => $this->type,
            'parent_node_id' => $this->parentNodeId,
            'count' => $this->count,
        ];
    }
}
