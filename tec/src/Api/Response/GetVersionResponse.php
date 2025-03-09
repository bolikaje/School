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

namespace ItPremium\TecDoc\Api\Response;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GetVersionResponse extends AbstractTecDocResponse
{
    /**
     * @var string
     */
    protected string $supplierDataVersion;

    /**
     * @var string
     */
    protected string $referenceDataVersion;

    /**
     * @var string
     */
    protected string $buildDate;

    /**
     * @var string
     */
    protected string $buildVersion;

    /**
     * @return string
     */
    public function getSupplierDataVersion(): string
    {
        return $this->supplierDataVersion;
    }

    /**
     * @param string $supplierDataVersion
     *
     * @return $this
     */
    public function setSupplierDataVersion(string $supplierDataVersion): static
    {
        $this->supplierDataVersion = $supplierDataVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getReferenceDataVersion(): string
    {
        return $this->referenceDataVersion;
    }

    /**
     * @param string $referenceDataVersion
     *
     * @return $this
     */
    public function setReferenceDataVersion(string $referenceDataVersion): static
    {
        $this->referenceDataVersion = $referenceDataVersion;

        return $this;
    }

    /**
     * @return string
     */
    public function getBuildDate(): string
    {
        return $this->buildDate;
    }

    /**
     * @param string $buildDate
     *
     * @return $this
     */
    public function setBuildDate(string $buildDate): static
    {
        $this->buildDate = $buildDate;

        return $this;
    }

    /**
     * @return string
     */
    public function getBuildVersion(): string
    {
        return $this->buildVersion;
    }

    /**
     * @param string $buildVersion
     *
     * @return $this
     */
    public function setBuildVersion(string $buildVersion): static
    {
        $this->buildVersion = $buildVersion;

        return $this;
    }

    /**
     * @param array $response
     *
     * @return static
     */
    public static function fromApiResponse(array $response): static
    {
        return parent::fromApiResponse($response)
            ->setSupplierDataVersion($response['supplierDataVersion'])
            ->setReferenceDataVersion($response['referenceDataVersion'])
            ->setBuildDate($response['buildDate'])
            ->setBuildVersion($response['buildVersion']);
    }
}
