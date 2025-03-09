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
use ItPremium\TecDoc\Entity\Doctrine\Interface\TecdocEntityWithRateInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="ItPremium\TecDoc\Repository\DiscountRepository")
 */
class TecdocDiscount implements TecdocEntityWithRateInterface
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_tecdoc_discount", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var TecdocSupplier
     *
     * @ORM\ManyToOne(targetEntity="TecdocSupplier", inversedBy="tecdocDiscounts")
     *
     * @ORM\JoinColumn(name="id_tecdoc_supplier", referencedColumnName="id_tecdoc_supplier", nullable=false)
     */
    private TecdocSupplier $tecdocSupplier;

    /**
     * @var ?int
     *
     * @ORM\Column(name="id_group", type="integer", length=11, nullable=true)
     */
    private ?int $groupId;

    /**
     * @var ?string
     *
     * @ORM\Column(name="brand", type="string", length=255, nullable=true)
     */
    private ?string $brand;

    /**
     * @var float
     *
     * @ORM\Column(name="discount", type="float", length=25)
     */
    private float $discount;

    /**
     * @var ?float
     *
     * @ORM\Column(name="price_range_start", type="float", length=25, nullable=true)
     */
    private ?float $priceRangeStart;

    /**
     * @var ?float
     *
     * @ORM\Column(name="price_range_end", type="float", length=25, nullable=true)
     */
    private ?float $priceRangeEnd;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", length=1)
     */
    private bool $active;

    /**
     * @var int
     */
    private int $weight;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return TecdocSupplier
     */
    public function getTecdocSupplier(): TecdocSupplier
    {
        return $this->tecdocSupplier;
    }

    /**
     * @param TecdocSupplier $tecdocSupplier
     *
     * @return $this
     */
    public function setTecdocSupplier(TecdocSupplier $tecdocSupplier): static
    {
        $this->tecdocSupplier = $tecdocSupplier;

        return $this;
    }

    /**
     * @return ?int
     */
    public function getGroupId(): ?int
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     *
     * @return $this
     */
    public function setGroupId(int $groupId): static
    {
        $this->groupId = $groupId;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getBrand(): ?string
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
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @param float $discount
     *
     * @return $this
     */
    public function setDiscount(float $discount): static
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * @return ?float
     */
    public function getPriceRangeStart(): ?float
    {
        return $this->priceRangeStart;
    }

    /**
     * @param float $priceRangeStart
     *
     * @return $this
     */
    public function setPriceRangeStart(float $priceRangeStart): static
    {
        $this->priceRangeStart = $priceRangeStart;

        return $this;
    }

    /**
     * @return ?float
     */
    public function getPriceRangeEnd(): ?float
    {
        return $this->priceRangeEnd;
    }

    /**
     * @param float $priceRangeEnd
     *
     * @return $this
     */
    public function setPriceRangeEnd(float $priceRangeEnd): static
    {
        $this->priceRangeEnd = $priceRangeEnd;

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
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     *
     * @return $this
     */
    public function setWeight(int $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return float
     */
    public function getRate(): float
    {
        return $this->getDiscount();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id_tecdoc_discount' => $this->getId(),
            'id_group' => $this->getGroupId(),
            'tecdoc_supplier' => $this->getTecdocSupplier(),
            'brand' => $this->getBrand(),
            'discount' => $this->getDiscount(),
            'price_range_start' => $this->getPriceRangeStart(),
            'price_range_end' => $this->getPriceRangeEnd(),
            'active' => $this->getActive(),
        ];
    }
}
