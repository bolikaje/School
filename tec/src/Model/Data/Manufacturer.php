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

if (!defined('_PS_VERSION_')) {
    exit;
}

final class Manufacturer
{
    /**
     * Manufacturer constructor.
     *
     * @param int $id
     * @param string $name
     * @param ?string $linkingTargetTypes
     */
    public function __construct(
        /** @var int */
        public readonly int $id,

        /** @var string */
        public readonly string $name,

        /** @var ?string */
        public readonly ?string $linkingTargetTypes,
    ) {
    }

    /** @var bool */
    public bool $active = true;

    /**
     * @param bool $fallbackImage
     *
     * @return string|bool
     */
    public function getImage(bool $fallbackImage = true): string|bool
    {
        $directory = 'itp_tecdoc/views/img/manufacturers/';

        if (file_exists(_PS_MODULE_DIR_ . $directory . $this->id . '.png')) {
            return _MODULE_DIR_ . $directory . $this->id . '.png';
        }

        return $fallbackImage ? _MODULE_DIR_ . $directory . 'no-image.jpg' : false;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return \Tools::str2url($this->name);
    }

    /**
     * @param LinkingTargetType $linkingTargetType
     * @param ?int $langId
     *
     * @return string
     */
    public function getLink(LinkingTargetType $linkingTargetType = LinkingTargetType::PASSENGER, ?int $langId = null): string
    {
        return \Context::getContext()->link->getModuleLink('itp_tecdoc', 'modelSeries', [
            'linking_target_type_slug' => $linkingTargetType->slug(),
            'manufacturer_slug' => $this->getSlug(),
        ], true, $langId);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'linkingTargetTypes' => $this->linkingTargetTypes,
            'active' => $this->active,
        ];
    }
}
