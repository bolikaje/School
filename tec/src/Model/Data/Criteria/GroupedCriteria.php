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

namespace ItPremium\TecDoc\Model\Data\Criteria;

use Doctrine\Common\Collections\ArrayCollection;
use ItPremium\TecDoc\Enum\CriteriaType;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class GroupedCriteria
{
    /**
     * GroupedCriteria constructor.
     *
     * @param int $id
     * @param string $description
     * @param CriteriaType $type
     * @param ArrayCollection<int, CriteriaValue> $values
     * @param bool $isInterval
     * @param bool $isMandatory
     */
    public function __construct(
        /** @var int */
        public int $id,

        /** @var string */
        public string $description,

        /** @var CriteriaType */
        public CriteriaType $type,

        /** @var bool */
        public bool $isInterval,

        /** @var bool */
        public bool $isMandatory,

        /** @var ArrayCollection<int, CriteriaValue> */
        public ArrayCollection $values = new ArrayCollection(),
    ) {
    }

    /**
     * @param CriteriaValue $criteriaValue
     *
     * @return $this
     */
    public function addCriteriaValue(CriteriaValue $criteriaValue): GroupedCriteria
    {
        $this->values->add($criteriaValue);

        return $this;
    }
}
