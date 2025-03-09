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

namespace ItPremium\TecDoc\Model\Data\Trait;

if (!defined('_PS_VERSION_')) {
    exit;
}

trait HasProductionYears
{
    /**
     * @var ?int
     */
    public readonly ?int $yearFrom;

    /**
     * @var ?string
     */
    public readonly ?string $monthFrom;

    /**
     * @var ?int
     */
    public ?int $yearTo;

    /**
     * @var ?string
     */
    public ?string $monthTo;

    /**
     * @var bool
     */
    public bool $stillInProduction = false;

    /**
     * @return void
     */
    public function initTrait(): void
    {
        if ($this->yearFrom and !$this->yearTo) {
            $this->yearTo = (int) date('Y');
            $this->monthTo = date('m');

            $this->stillInProduction = true;
        }
    }

    /**
     * @return string
     */
    public function getDateFromLabel(): string
    {
        $dateArr = array_filter([
            $this->monthFrom,
            $this->yearFrom,
        ]);

        return implode('.', $dateArr) ?: '...';
    }

    /**
     * @return string
     */
    public function getDateToLabel(): string
    {
        $dateArr = array_filter([
            $this->monthTo,
            $this->yearTo,
        ]);

        return $this->stillInProduction ? '...' : implode('.', $dateArr);
    }

    public function getYearsLabel(): string
    {
        return sprintf('%s - %s', $this->getDateFromLabel(), $this->getDateToLabel());
    }
}
