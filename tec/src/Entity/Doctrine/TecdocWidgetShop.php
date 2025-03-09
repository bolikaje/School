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
use PrestaShopBundle\Entity\Shop;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @ORM\Table()
 *
 * @ORM\Entity()
 */
class TecdocWidgetShop
{
    /**
     * @var TecdocWidget
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="ItPremium\TecDoc\Entity\Doctrine\TecdocWidget", inversedBy="tecdocWidgetShops")
     *
     * @ORM\JoinColumn(name="id_tecdoc_widget", referencedColumnName="id_tecdoc_widget", nullable=false)
     */
    private TecdocWidget $tecdocWidget;

    /**
     * @var Shop
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Shop")
     *
     * @ORM\JoinColumn(name="id_shop", referencedColumnName="id_shop", nullable=false)
     */
    private Shop $shop;

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
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean", length=1)
     */
    private bool $active = true;

    /**
     * @return TecdocWidget
     */
    public function getTecdocWidget(): TecdocWidget
    {
        return $this->tecdocWidget;
    }

    /**
     * @param TecdocWidget $tecdocWidget
     *
     * @return $this
     */
    public function setTecdocWidget(TecdocWidget $tecdocWidget): static
    {
        $this->tecdocWidget = $tecdocWidget;

        return $this;
    }

    /**
     * @return Shop
     */
    public function getShop(): Shop
    {
        return $this->shop;
    }

    /**
     * @param Shop $shop
     *
     * @return $this
     */
    public function setShop(Shop $shop): static
    {
        $this->shop = $shop;

        return $this;
    }

    /**
     * @return ?string
     */
    public function getCustomId(): ?string
    {
        return $this->customId;
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
        return $this->customCssClass;
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
}
