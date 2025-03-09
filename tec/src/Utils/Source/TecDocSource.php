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

namespace ItPremium\TecDoc\Utils\Source;

use ItPremium\TecDoc\Utils\Helper;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class TecDocSource implements \IteratorAggregate
{
    /**
     * @var iterable
     */
    private iterable $source;

    /**
     * TecDocSource constructor.
     *
     * @param iterable $source
     */
    public function __construct(iterable $source)
    {
        $this->source = $this->transform($source);
    }

    /**
     * @param iterable $source
     *
     * @return iterable
     */
    private function transform(iterable $source): iterable
    {
        $result = [];

        foreach ($source as $key => $value) {
            if (is_iterable($value)) {
                $value = $this->transform($value);
            }

            if ($key == 'name' or $key == 'description') {
                $value = Helper::prepareName($value);
            }

            if ($key == 'cylinderCapacityLiter') {
                $value = number_format($value / 100, 1, '.', '');
            }

            if ($key == 'yearFrom' or $key == 'yearTo' or $key == 'beginYearMonth' or $key == 'endYearMonth') {
                $value = Helper::extractYearFromTecDocDate($value);
            }

            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @return \Traversable
     */
    public function getIterator(): \Traversable
    {
        yield from $this->source;
    }
}
