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
 * @ORM\Table(uniqueConstraints={@ORM\UniqueConstraint(name="unique", columns={"id_tecdoc_supplier", "brand", "reference"})})
 *
 * @ORM\Entity(repositoryClass="ItPremium\TecDoc\Repository\StockRepository")
 */
class TecdocStock
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_tecdoc_stock", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var TecdocSupplier
     *
     * @ORM\ManyToOne(targetEntity="TecdocSupplier", inversedBy="tecdocStocks")
     *
     * @ORM\JoinColumn(name="id_tecdoc_supplier", referencedColumnName="id_tecdoc_supplier", nullable=false)
     */
    private TecdocSupplier $tecdocSupplier;

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
     * @var ?string
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=true)
     */
    private ?string $name;

    /**
     * @var ?float
     *
     * @ORM\Column(name="wholesale_price", type="float", length=20)
     */
    private ?float $wholesalePrice;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="float", length=20)
     */
    private float $price;

    /**
     * @var ?float
     *
     * @ORM\Column(name="deposit", type="float", length=20)
     */
    private ?float $deposit;

    /**
     * @var int
     *
     * @ORM\Column(name="minimum_order_quantity", type="integer", length=11, options={"default" : 1})
     */
    private int $minimumOrderQuantity = 1;

    /**
     * @var bool
     *
     * @ORM\Column(name="enforce_quantity_multiple", type="boolean", length=1, options={"default" : 0})
     */
    private bool $enforceQuantityMultiple;

    /**
     * @var int
     *
     * @ORM\Column(name="stock", type="integer", length=11)
     */
    private int $stock;

    /**
     * @var int
     *
     * @ORM\Column(name="delivery_time", type="integer")
     */
    private int $deliveryTime;

    /**
     * @var ?float
     *
     * @ORM\Column(name="weight", type="float", length=20)
     */
    private ?float $weight;

    /**
     * @var bool
     *
     * @ORM\Column(name="oem", type="boolean", length=1, options={"default" : 0})
     */
    private bool $oem;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", length=1)
     */
    private bool $active;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_import", type="datetime")
     */
    private \DateTime $dateImport;

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
     * @return ?string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ?float
     */
    public function getWholesalePrice(): ?float
    {
        return $this->wholesalePrice;
    }

    /**
     * @param float $wholesalePrice
     *
     * @return $this
     */
    public function setWholesalePrice(float $wholesalePrice): static
    {
        $this->wholesalePrice = $wholesalePrice;

        return $this;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $price
     *
     * @return $this
     */
    public function setPrice(float $price): static
    {
        $this->price = $price;

        return $this;
    }

    /**
     * @return ?float
     */
    public function getDeposit(): ?float
    {
        return $this->deposit;
    }

    /**
     * @param float $deposit
     *
     * @return $this
     */
    public function setDeposit(float $deposit): static
    {
        $this->deposit = $deposit;

        return $this;
    }

    /**
     * @return int
     */
    public function getMinimumOrderQuantity(): int
    {
        return $this->minimumOrderQuantity;
    }

    /**
     * @param int $minimumOrderQuantity
     *
     * @return $this
     */
    public function setMinimumOrderQuantity(int $minimumOrderQuantity): static
    {
        $this->stock = $minimumOrderQuantity;

        return $this;
    }

    /**
     * @return bool
     */
    public function getEnforceQuantityMultiple(): bool
    {
        return $this->enforceQuantityMultiple;
    }

    /**
     * @param bool $enforceQuantityMultiple
     *
     * @return $this
     */
    public function setEnforceQuantityMultiple(bool $enforceQuantityMultiple): static
    {
        $this->enforceQuantityMultiple = $enforceQuantityMultiple;

        return $this;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @param int $stock
     *
     * @return $this
     */
    public function setStock(int $stock): static
    {
        $this->stock = $stock;

        return $this;
    }

    /**
     * @return int
     */
    public function getDeliveryTime(): int
    {
        return $this->deliveryTime;
    }

    /**
     * @param int $deliveryTime
     *
     * @return $this
     */
    public function setDeliveryTime(int $deliveryTime): static
    {
        $this->deliveryTime = $deliveryTime;

        return $this;
    }

    /**
     * @return ?float
     */
    public function getWeight(): ?float
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     *
     * @return $this
     */
    public function setWeight(float $weight): static
    {
        $this->weight = $weight;

        return $this;
    }

    /**
     * @return bool
     */
    public function getOem(): bool
    {
        return $this->oem;
    }

    /**
     * @param bool $oem
     *
     * @return $this
     */
    public function setOem(bool $oem): static
    {
        $this->oem = $oem;

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
     * @return \DateTime
     */
    public function getDateImport(): \DateTime
    {
        return $this->dateImport;
    }

    /**
     * @param \DateTime $dateImport
     *
     * @return $this
     */
    public function setDateImport(\DateTime $dateImport): static
    {
        $this->dateImport = $dateImport;

        return $this;
    }

    /**
     * @param array $tecdocStock
     *
     * @return TecdocStock
     */
    public static function fromArray(array $tecdocStock): TecdocStock
    {
        $tecdocSupplier = (new TecdocSupplier())
            ->setId((int) $tecdocStock['id_tecdoc_supplier']);

        return (new TecdocStock())
            ->setTecdocSupplier($tecdocSupplier)
            ->setBrand((string) $tecdocStock['brand'])
            ->setReference((string) $tecdocStock['reference'])
            ->setName((string) $tecdocStock['name'])
            ->setWholesalePrice((float) $tecdocStock['wholesale_price'])
            ->setPrice((float) $tecdocStock['price'])
            ->setDeposit((float) $tecdocStock['deposit'])
            ->setMinimumOrderQuantity((int) $tecdocStock['minimum_order_quantity'])
            ->setEnforceQuantityMultiple((bool) $tecdocStock['enforce_quantity_multiple'])
            ->setStock((int) $tecdocStock['stock'])
            ->setDeliveryTime((int) $tecdocStock['delivery_time'])
            ->setWeight((float) $tecdocStock['weight'])
            ->setOem((bool) $tecdocStock['oem'])
            ->setActive((bool) $tecdocStock['active']);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id_tecdoc_stock' => $this->getId(),
            'tecdoc_supplier' => $this->getTecdocSupplier(),
            'brand' => $this->getBrand(),
            'reference' => $this->getReference(),
            'name' => $this->getName(),
            'wholesale_price' => $this->getWholesalePrice(),
            'price' => $this->getPrice(),
            'deposit' => $this->getDeposit(),
            'minimum_order_quantity' => $this->getMinimumOrderQuantity(),
            'enforce_quantity_multiple' => $this->getEnforceQuantityMultiple(),
            'stock' => $this->getStock(),
            'delivery_time' => $this->getDeliveryTime(),
            'weight' => $this->getWeight(),
            'oem' => $this->getOem(),
            'active' => $this->getActive(),
            'date_import' => $this->getDateImport(),
        ];
    }
}
