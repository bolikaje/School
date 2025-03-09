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
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="unique", columns={"brand", "reference", "cross_brand", "cross_reference"})})
 *
 * @ORM\Entity(repositoryClass="ItPremium\TecDoc\Repository\CrossRepository")
 */
class TecdocCross
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_tecdoc_cross", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var string
     *
     * @ORM\Column(name="brand", type="string", length=255)
     */
    private string $brand;

    /**
     * @var string
     *
     * @ORM\Column(name="reference", type="string", length=255)
     */
    private string $reference;

    /**
     * @var string
     *
     * @ORM\Column(name="cross_brand", type="string", length=255)
     */
    private string $crossBrand;

    /**
     * @var string
     *
     * @ORM\Column(name="cross_reference", type="string", length=255)
     */
    private string $crossReference;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="int", length=1)
     */
    private int $type;

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
     * @return string
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     *
     * @return $this
     */
    public function setBrand(string $brand): static
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     *
     * @return $this
     */
    public function setReference(string $reference): static
    {
        $this->reference = $reference;

        return $this;
    }

    /**
     * @return string
     */
    public function getCrossBrand(): string
    {
        return $this->crossBrand;
    }

    /**
     * @param string $crossBrand
     *
     * @return $this
     */
    public function setCrossBrand(string $crossBrand): static
    {
        $this->crossBrand = $crossBrand;

        return $this;
    }

    /**
     * @return string
     */
    public function getCrossReference(): string
    {
        return $this->crossReference;
    }

    /**
     * @param string $crossReference
     *
     * @return $this
     */
    public function setCrossReference(string $crossReference): static
    {
        $this->crossReference = $crossReference;

        return $this;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     *
     * @return $this
     */
    public function setType(int $type): static
    {
        $this->type = $type;

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
            'id_tecdoc_cross' => $this->getId(),
            'brand' => $this->getBrand(),
            'reference' => $this->getReference(),
            'cross_brand' => $this->getCrossBrand(),
            'cross_reference' => $this->getCrossReference(),
            'type' => $this->getType(),
            'active' => $this->getActive(),
        ];
    }
}
