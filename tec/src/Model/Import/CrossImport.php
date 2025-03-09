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

namespace ItPremium\TecDoc\Model\Import;

use ItPremium\TecDoc\Model\Import\Interface\ImportEntityInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CrossImport implements ImportEntityInterface
{
    /**
     * CrossImport constructor.
     *
     * @param string $brand
     * @param string $reference
     * @param string $crossBrand
     * @param string $crossReference
     * @param bool $active
     */
    public function __construct(
        private readonly string $brand,
        private readonly string $reference,
        private readonly string $crossBrand,
        private readonly string $crossReference,
        private readonly bool $active,
    ) {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'brand' => $this->brand,
            'reference' => $this->reference,
            'cross_brand' => $this->crossBrand,
            'cross_reference' => $this->crossReference,
            'active' => $this->active,
        ];
    }
}
