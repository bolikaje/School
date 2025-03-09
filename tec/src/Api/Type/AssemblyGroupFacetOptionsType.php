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

namespace ItPremium\TecDoc\Api\Type;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AssemblyGroupFacetOptionsType extends AbstractTecDocType
{
    /**
     * @var bool
     */
    protected bool $enabled = true;

    /**
     * @var string
     */
    protected string $assemblyGroupType;

    /**
     * @var bool
     */
    protected bool $includeCompleteTree = true;

    /**
     * @var int
     */
    protected int $maxDepth;

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     *
     * @return $this
     */
    public function setEnabled(bool $enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @return string
     */
    public function getAssemblyGroupType(): string
    {
        return $this->assemblyGroupType;
    }

    /**
     * @param string $assemblyGroupType
     *
     * @return $this
     */
    public function setAssemblyGroupType(string $assemblyGroupType): static
    {
        $this->assemblyGroupType = $assemblyGroupType;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIncludeCompleteTree(): bool
    {
        return $this->includeCompleteTree;
    }

    /**
     * @param bool $includeCompleteTree
     *
     * @return $this
     */
    public function setIncludeCompleteTree(bool $includeCompleteTree): static
    {
        $this->includeCompleteTree = $includeCompleteTree;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxDepth(): int
    {
        return $this->maxDepth;
    }

    /**
     * @param int $maxDepth
     *
     * @return $this
     */
    public function setMaxDepth(int $maxDepth): static
    {
        $this->maxDepth = $maxDepth;

        return $this;
    }
}
