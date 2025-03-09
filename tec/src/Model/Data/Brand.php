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

use ItPremium\TecDoc\Enum\BrandQuality;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class Brand
{
    /**
     * Brand constructor.
     *
     * @param int $id
     * @param ?string $name
     * @param ?BrandAddress $address
     * @param ?ImageRecord $image
     */
    public function __construct(
        /** @var int */
        public readonly int $id,

        /** @var ?string */
        public readonly ?string $name,

        /** @var ?BrandAddress */
        public readonly ?BrandAddress $address,

        /** @var ?ImageRecord */
        public readonly ?ImageRecord $image,
    ) {
    }

    /**
     * @var BrandQuality
     */
    public BrandQuality $quality = BrandQuality::NONE;

    /**
     * @var bool
     */
    public bool $active = true;

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return \Tools::str2url($this->name);
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        $directory = 'itp_tecdoc/views/img/brands/';

        if (file_exists(_PS_MODULE_DIR_ . $directory . $this->id . '.webp')) {
            return _MODULE_DIR_ . $directory . $this->id . '.webp';
        } elseif ($this->image) {
            return $this->image->getImageUrl();
        }

        return _MODULE_DIR_ . $directory . 'no-image.jpg';
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'quality' => $this->quality,
            'active' => $this->active,
        ];
    }
}
