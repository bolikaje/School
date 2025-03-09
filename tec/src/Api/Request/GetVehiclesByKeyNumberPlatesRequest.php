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

namespace ItPremium\TecDoc\Api\Request;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GetVehiclesByKeyNumberPlatesRequest extends AbstractTecDocRequest
{
    /**
     * @var string
     */
    protected string $companyName;

    /**
     * @var bool
     */
    protected bool $details;

    /**
     * @var string
     */
    protected string $endCustomerUsername;

    /**
     * @var string
     */
    protected string $endCustomerPassword;

    /**
     * @var string
     */
    protected string $keyId;

    /**
     * @var string
     */
    protected string $initials;

    /**
     * @var string
     */
    protected string $keySystemNumber;

    /**
     * @var int
     */
    protected int $keySystemType;

    /**
     * @var string
     */
    protected string $mandatorUsername;

    /**
     * @var string
     */
    protected string $mandatorPassword;

    /**
     * @var bool
     */
    protected bool $picture;

    /**
     * @var string
     */
    protected string $productId;

    /**
     * @var string
     */
    protected string $serviceName;

    /**
     * @return string
     */
    public function getCompanyName(): string
    {
        return $this->companyName;
    }

    /**
     * @param string $companyName
     *
     * @return $this
     */
    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    /**
     * @return bool
     */
    public function isDetails(): bool
    {
        return $this->details;
    }

    /**
     * @param bool $details
     *
     * @return $this
     */
    public function setDetails(bool $details): static
    {
        $this->details = $details;

        return $this;
    }

    /**
     * @return string
     */
    public function getEndCustomerUsername(): string
    {
        return $this->endCustomerUsername;
    }

    /**
     * @param string $endCustomerUsername
     *
     * @return $this
     */
    public function setEndCustomerUsername(string $endCustomerUsername): static
    {
        $this->endCustomerUsername = $endCustomerUsername;

        return $this;
    }

    /**
     * @return string
     */
    public function getEndCustomerPassword(): string
    {
        return $this->endCustomerPassword;
    }

    /**
     * @param string $endCustomerPassword
     *
     * @return $this
     */
    public function setEndCustomerPassword(string $endCustomerPassword): static
    {
        $this->endCustomerPassword = $endCustomerPassword;

        return $this;
    }

    /**
     * @return string
     */
    public function getKeyId(): string
    {
        return $this->keyId;
    }

    /**
     * @param string $keyId
     *
     * @return $this
     */
    public function setKeyId(string $keyId): static
    {
        $this->keyId = $keyId;

        return $this;
    }

    /**
     * @return string
     */
    public function getInitials(): string
    {
        return $this->initials;
    }

    /**
     * @param string $initials
     *
     * @return $this
     */
    public function setInitials(string $initials): static
    {
        $this->initials = $initials;

        return $this;
    }

    /**
     * @return string
     */
    public function getKeySystemNumber(): string
    {
        return $this->keySystemNumber;
    }

    /**
     * @param string $keySystemNumber
     *
     * @return $this
     */
    public function setKeySystemNumber(string $keySystemNumber): static
    {
        $this->keySystemNumber = $keySystemNumber;

        return $this;
    }

    /**
     * @return int
     */
    public function getKeySystemType(): int
    {
        return $this->keySystemType;
    }

    /**
     * @param int $keySystemType
     *
     * @return $this
     */
    public function setKeySystemType(int $keySystemType): static
    {
        $this->keySystemType = $keySystemType;

        return $this;
    }

    /**
     * @return string
     */
    public function getMandatorUsername(): string
    {
        return $this->mandatorUsername;
    }

    /**
     * @param string $mandatorUsername
     *
     * @return $this
     */
    public function setMandatorUsername(string $mandatorUsername): static
    {
        $this->mandatorUsername = $mandatorUsername;

        return $this;
    }

    /**
     * @return string
     */
    public function getMandatorPassword(): string
    {
        return $this->mandatorPassword;
    }

    /**
     * @param string $mandatorPassword
     *
     * @return $this
     */
    public function setMandatorPassword(string $mandatorPassword): static
    {
        $this->mandatorPassword = $mandatorPassword;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPicture(): bool
    {
        return $this->picture;
    }

    /**
     * @param bool $picture
     *
     * @return $this
     */
    public function setPicture(bool $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @param string $productId
     *
     * @return $this
     */
    public function setProductId(string $productId): static
    {
        $this->productId = $productId;

        return $this;
    }

    /**
     * @return string
     */
    public function getServiceName(): string
    {
        return $this->serviceName;
    }

    /**
     * @param string $serviceName
     *
     * @return $this
     */
    public function setServiceName(string $serviceName): static
    {
        $this->serviceName = $serviceName;

        return $this;
    }
}
