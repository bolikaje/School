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

namespace ItPremium\TecDoc\Model\Widget;

use Doctrine\Common\Collections\ArrayCollection;
use ItPremium\TecDoc\Model\Data\Brand;

if (!defined('_PS_VERSION_')) {
    exit;
}

class BrandsListWidget extends AbstractWidget
{
    /**
     * @var string
     */
    private string $templateFile = 'brands-list';

    /**
     * BrandsListWidget constructor.
     *
     * @param ArrayCollection<int, Brand> $brands
     */
    public function __construct(
        private ArrayCollection $brands,
    ) {
    }

    /**
     * @return array
     */
    public function getTemplateVariables(): array
    {
        return [
            'brands' => $this->getBrands(),
        ];
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return $this->templateFile;
    }

    /**
     * @return ArrayCollection<int, Brand>
     */
    public function getBrands(): ArrayCollection
    {
        return $this->brands;
    }

    /**
     * @param ArrayCollection<int, Brand> $brands
     */
    public function setBrands(ArrayCollection $brands): void
    {
        $this->brands = $brands;
    }

    /**
     * @param Brand $brand
     *
     * @return $this
     */
    public function addBrand(Brand $brand): static
    {
        $this->brands->add($brand);

        return $this;
    }
}
