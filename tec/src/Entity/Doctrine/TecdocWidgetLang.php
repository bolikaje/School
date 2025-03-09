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
use PrestaShopBundle\Entity\Lang;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @ORM\Table()
 *
 * @ORM\Entity()
 */
class TecdocWidgetLang
{
    /**
     * @var TecdocWidget
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="ItPremium\TecDoc\Entity\Doctrine\TecdocWidget", inversedBy="languages")
     *
     * @ORM\JoinColumn(name="id_tecdoc_widget", referencedColumnName="id_tecdoc_widget", nullable=false)
     */
    private TecdocWidget $tecdocWidget;

    /**
     * @var Lang
     *
     * @ORM\Id
     *
     * @ORM\ManyToOne(targetEntity="PrestaShopBundle\Entity\Lang")
     *
     * @ORM\JoinColumn(name="id_lang", referencedColumnName="id_lang", nullable=false, onDelete="CASCADE")
     */
    private Lang $lang;

    /**
     * @var string
     *
     * @ORM\Column(name="public_name", type="string", length=255, nullable=true)
     */
    private string $publicName;

    /**
     * @var string
     *
     * @ORM\Column(name="custom_html", type="text")
     */
    private string $customHtml;

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
     * @return Lang
     */
    public function getLang(): Lang
    {
        return $this->lang;
    }

    /**
     * @param Lang $lang
     *
     * @return $this
     */
    public function setLang(Lang $lang): static
    {
        $this->lang = $lang;

        return $this;
    }

    /**
     * @return string
     */
    public function getPublicName(): string
    {
        return $this->publicName;
    }

    /**
     * @param string $publicName
     *
     * @return $this
     */
    public function setPublicName(string $publicName): static
    {
        $this->publicName = $publicName;

        return $this;
    }

    /**
     * @return string
     */
    public function getCustomHtml(): string
    {
        return $this->customHtml;
    }

    /**
     * @param string $customHtml
     *
     * @return $this
     */
    public function setCustomHtml(string $customHtml): static
    {
        $this->customHtml = $customHtml;

        return $this;
    }
}
