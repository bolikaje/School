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

class StockImport implements ImportEntityInterface
{
    /**
     * StockImport constructor.
     *
     * @param int $tecdocSupplierId
     * @param string $brand
     * @param string $reference
     * @param ?string $name
     * @param ?float $wholesalePrice
     * @param float $price
     * @param ?float $deposit
     * @param int $stock
     * @param int $deliveryTime
     * @param ?float $weight
     * @param bool $oem
     * @param bool $active
     * @param ?int $minimumOrderQuantity
     * @param bool $enforceQuantityMultiple
     * @param ?string $dateImport
     */
    public function __construct(
        private readonly int $tecdocSupplierId,
        private readonly string $brand,
        private readonly string $reference,
        private readonly float $price,
        private readonly int $stock,
        private readonly int $deliveryTime,
        private readonly ?string $name = null,
        private readonly ?float $wholesalePrice = null,
        private readonly ?float $deposit = null,
        private readonly ?float $weight = null,
        private readonly bool $oem = false,
        private readonly bool $active = true,
        private ?int $minimumOrderQuantity = 1,
        private readonly bool $enforceQuantityMultiple = false,
        private ?string $dateImport = null,
    ) {
        if (!$this->minimumOrderQuantity or $this->minimumOrderQuantity <= 0) {
            $this->minimumOrderQuantity = 1;
        }

        if (!$this->dateImport) {
            $this->dateImport = date('Y-m-d H:i:s');
        }
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id_tecdoc_supplier' => $this->tecdocSupplierId,
            'brand' => $this->brand,
            'reference' => $this->reference,
            'name' => $this->name,
            'wholesale_price' => $this->wholesalePrice,
            'price' => $this->price,
            'deposit' => $this->deposit,
            'minimum_order_quantity' => $this->minimumOrderQuantity,
            'enforce_quantity_multiple' => $this->enforceQuantityMultiple,
            'stock' => $this->stock,
            'delivery_time' => $this->deliveryTime,
            'weight' => $this->weight,
            'oem' => $this->oem,
            'active' => $this->active,
            'date_import' => $this->dateImport,
        ];
    }
}
