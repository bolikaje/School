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

namespace ItPremium\TecDoc\Entity\Doctrine;

use Doctrine\ORM\Mapping as ORM;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="ItPremium\TecDoc\Repository\ManufacturerStatusRepository")
 */
class TecdocManufacturerStatus
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_tecdoc_manufacturer_status", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_tecdoc_manufacturer", type="integer", length=11)
     */
    private int $tecdocManufacturerId;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", length=1)
     */
    private bool $active;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getTecdocManufacturerId(): int
    {
        return $this->tecdocManufacturerId;
    }

    /**
     * @param int $tecdocManufacturerId
     *
     * @return $this
     */
    public function setTecdocManufacturerId(int $tecdocManufacturerId): static
    {
        $this->tecdocManufacturerId = $tecdocManufacturerId;

        return $this;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     *
     * @return $this
     */
    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id_tecdoc_manufacturer_status' => $this->getId(),
            'id_tecdoc_manufacturer' => $this->getTecdocManufacturerId(),
            'active' => $this->getActive(),
        ];
    }
}
