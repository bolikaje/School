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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ItPremium\TecDoc\Enum\Orientation;
use ItPremium\TecDoc\Model\Widget\Interface\WidgetInterface;
use PrestaShopBundle\Entity\Shop;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @ORM\Table()
 *
 * @ORM\Entity(repositoryClass="ItPremium\TecDoc\Repository\WidgetRepository")
 */
class TecdocWidget
{
    /**
     * @var int
     *
     * @ORM\Id
     *
     * @ORM\Column(name="id_tecdoc_widget", type="integer")
     *
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private int $id;

    /**
     * @var int
     *
     * @ORM\Column(name="id_hook", type="integer", length=11)
     */
    private int $hookId;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private string $name;

    /**
     * @var int
     *
     * @ORM\Column(name="type", type="integer", length=1)
     */
    private int $type;

    /**
     * @var int
     *
     * @ORM\Column(name="orientation", type="integer", length=1)
     */
    private int $orientation;

    /**
     * @var bool
     *
     * @ORM\Column(name="show_linkage_target_types", type="boolean", length=1, options={"default" : 0})
     */
    private bool $showLinkageTargetTypes = true;

    /**
     * @var string
     *
     * @ORM\Column(name="assembly_groups", type="text")
     */
    private string $assemblyGroups;

    /**
     * @var string
     *
     * @ORM\Column(name="manufacturers", type="text")
     */
    private string $manufacturers;

    /**
     * @var string
     *
     * @ORM\Column(name="brands", type="text")
     */
    private string $brands;

    /**
     * @var ?string
     *
     * @ORM\Column(name="custom_id", type="text", length=255, nullable=true)
     */
    private ?string $customId;

    /**
     * @var ?string
     *
     * @ORM\Column(name="custom_css_class", type="text", length=255, nullable=true)
     */
    private ?string $customCssClass;

    /**
     * @var int
     *
     * @ORM\Column(name="position", type="integer", length=11)
     */
    private int $position;

    /**
     * @var bool
     *
     * @ORM\Column(name="show_public_name", type="boolean", length=1, options={"default" : 0})
     */
    private bool $showPublicName = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", length=1)
     */
    private bool $active = true;

    /**
     * @ORM\OneToMany(targetEntity="ItPremium\TecDoc\Entity\Doctrine\TecdocWidgetLang", cascade={"persist", "remove"}, mappedBy="tecdocWidget")
     */
    private $languages;

    /**
     * @ORM\OneToMany(targetEntity="ItPremium\TecDoc\Entity\Doctrine\TecdocWidgetShop", cascade={"persist", "remove"}, mappedBy="tecdocWidget")
     */
    private $shops;

    /**
     * @var WidgetInterface
     */
    private WidgetInterface $content;

    public function __construct()
    {
        $this->languages = new ArrayCollection();
        $this->shops = new ArrayCollection();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return int
     */
    public function getHookId(): int
    {
        return $this->hookId;
    }

    /**
     * @param int $hookId
     *
     * @return $this
     */
    public function setHookId(int $hookId): static
    {
        $this->hookId = $hookId;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
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
     * @return Orientation
     */
    public function getOrientation(): Orientation
    {
        return Orientation::tryFrom($this->orientation);
    }

    /**
     * @param int $orientation
     *
     * @return $this
     */
    public function setOrientation(int $orientation): static
    {
        $this->orientation = $orientation;

        return $this;
    }

    /**
     * @return bool
     */
    public function getShowLinkageTargetTypes(): bool
    {
        return $this->showLinkageTargetTypes;
    }

    /**
     * @param bool $showLinkageTargetTypes
     *
     * @return $this
     */
    public function setShowLinkageTargetTypes(bool $showLinkageTargetTypes): static
    {
        $this->showLinkageTargetTypes = $showLinkageTargetTypes;

        return $this;
    }

    /**
     * @return string
     */
    public function getAssemblyGroups(): string
    {
        return $this->assemblyGroups;
    }

    /**
     * @param string $assemblyGroups
     *
     * @return $this
     */
    public function setAssemblyGroups(string $assemblyGroups): static
    {
        $this->assemblyGroups = $assemblyGroups;

        return $this;
    }

    /**
     * @return string
     */
    public function getManufacturers(): string
    {
        return $this->manufacturers;
    }

    /**
     * @param string $manufacturers
     *
     * @return $this
     */
    public function setManufacturers(string $manufacturers): static
    {
        $this->manufacturers = $manufacturers;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getCustomId(): ?string
    {
        if ($this->shops->isEmpty()) {
            return $this->customId;
        }

        $tecdocWidgetShop = $this->shops->first();

        return $tecdocWidgetShop->getCustomId();
    }

    /**
     * @param string $customId
     *
     * @return $this
     */
    public function setCustomId(string $customId): static
    {
        $this->customId = $customId;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getCustomCssClass(): ?string
    {
        if ($this->shops->isEmpty()) {
            return $this->customCssClass;
        }

        $tecdocWidgetShop = $this->shops->first();

        return $tecdocWidgetShop->getCustomCssClass();
    }

    /**
     * @param string $customCssClass
     *
     * @return $this
     */
    public function setCustomCssClass(string $customCssClass): static
    {
        $this->customCssClass = $customCssClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getBrands(): string
    {
        return $this->brands;
    }

    /**
     * @param string $brands
     *
     * @return $this
     */
    public function setBrands(string $brands): static
    {
        $this->brands = $brands;

        return $this;
    }

    /**
     * @return int
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * @param int $position
     *
     * @return $this
     */
    public function setPosition(int $position): static
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return bool
     */
    public function getShowPublicName(): bool
    {
        return $this->showPublicName;
    }

    /**
     * @param bool $showPublicName
     *
     * @return $this
     */
    public function setShowPublicName(bool $showPublicName): static
    {
        $this->showPublicName = $showPublicName;

        return $this;
    }

    /**
     * @return WidgetInterface
     */
    public function getContent(): WidgetInterface
    {
        return $this->content;
    }

    /**
     * @param WidgetInterface $widgetInterface
     *
     * @return $this
     */
    public function setContent(WidgetInterface $widgetInterface): static
    {
        $this->content = $widgetInterface;

        return $this;
    }

    /**
     * @return bool
     */
    public function getActive(): bool
    {
        if ($this->shops->isEmpty()) {
            return $this->active;
        }

        $tecdocWidgetShop = $this->shops->first();

        return $tecdocWidgetShop->getActive();
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
     * @return Collection
     */
    public function getLanguages(): Collection
    {
        return $this->languages;
    }

    /**
     * @param int $langId
     *
     * @return ?TecdocWidgetLang
     */
    public function getLangById(int $langId): ?TecdocWidgetLang
    {
        foreach ($this->languages as $tecdocWidgetLang) {
            if ($langId === $tecdocWidgetLang->getLang()->getId()) {
                return $tecdocWidgetLang;
            }
        }

        return null;
    }

    /**
     * @param TecdocWidgetLang $tecdocWidgetLang
     *
     * @return $this
     */
    public function addLang(TecdocWidgetLang $tecdocWidgetLang): static
    {
        $tecdocWidgetLang->setTecdocWidget($this);
        $this->languages->add($tecdocWidgetLang);

        return $this;
    }

    /**
     * Get shops.
     *
     * @return Collection<int, Shop>
     */
    public function getShops(): Collection
    {
        return $this->shops;
    }

    /**
     * @param TecdocWidgetShop $tecdocWidgetShop
     *
     * @return $this
     */
    public function addShop(TecdocWidgetShop $tecdocWidgetShop): static
    {
        $tecdocWidgetShop->setTecdocWidget($this);
        $this->shops->add($tecdocWidgetShop);

        return $this;
    }

    /**
     * @return string
     */
    public function getPublicName(): string
    {
        if ($this->languages->isEmpty()) {
            return '';
        }

        $tecdocWidgetLanguage = $this->languages->first();

        return $tecdocWidgetLanguage->getPublicName();
    }

    /**
     * @return string
     */
    public function getCustomHtml(): string
    {
        if ($this->languages->isEmpty()) {
            return '';
        }

        $tecdocWidgetLanguage = $this->languages->first();

        return $tecdocWidgetLanguage->getCustomHtml();
    }
}
