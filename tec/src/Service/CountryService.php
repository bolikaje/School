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

namespace ItPremium\TecDoc\Service;

use CuyZ\Valinor\Mapper\MappingError;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use GuzzleHttp\Exception\GuzzleException;
use ItPremium\TecDoc\Api\Exception\TecDocApiException;
use ItPremium\TecDoc\Entity\Doctrine\TecdocCountry;
use ItPremium\TecDoc\Repository\Api\CountryRepository as CountryApiRepository;
use ItPremium\TecDoc\Repository\CountryRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class CountryService
{
    /**
     * CountryService constructor.
     *
     * @param CountryRepository $countryRepository
     * @param CountryApiRepository $countryApiRepository
     */
    public function __construct(
        private readonly CountryRepository $countryRepository,
        private readonly CountryApiRepository $countryApiRepository,
    ) {
    }

    /**
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function updateCountries(): void
    {
        $this
            ->countryRepository
            ->truncate();

        foreach ($this->countryApiRepository->getCountries() as $country) {
            $tecdocCountry = new TecdocCountry();
            $tecdocCountry->setName($country->name);
            $tecdocCountry->setCode($country->code);

            $this
                ->countryRepository
                ->save($tecdocCountry);
        }
    }
}
