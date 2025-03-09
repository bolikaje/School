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

use CuyZ\Valinor\Mapper\MappingError;
use GuzzleHttp\Exception\GuzzleException;
use ItPremium\TecDoc\Api\Exception\TecDocApiException;
use ItPremium\TecDoc\Entity\ManufacturerStatus;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminTecDocManufacturersController extends TecDocAdminController
{
    /**
     * AdminTecDocManufacturersController constructor.
     *
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = ManufacturerStatus::$definition['table'];
        $this->identifier = 'id';

        parent::__construct();
    }

    /**
     * @return string
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws PrestaShopException
     */
    public function renderList(): string
    {
        $fieldsList = [
            'id' => [
                'title' => $this->trans('ID', [], 'Modules.Itptecdoc.Admin'),
                'search' => false,
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Modules.Itptecdoc.Admin'),
                'search' => false,
            ],
            'active' => [
                'title' => $this->trans('Active', [], 'Modules.Itptecdoc.Admin'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'search' => false,
            ],
        ];

        $manufacturers = $this->tecdoc
            ->manufacturers()
            ->getManufacturers(false)
            ->map(function ($manufacturer) {
                return $manufacturer->toArray();
            })->toArray();

        $helperList = new HelperList();
        $helperList->title = $this->trans('Manufacturers', [], 'Modules.Itptecdoc.Admin');
        $helperList->identifier = 'id';
        $helperList->table = $this->table;
        $helperList->currentIndex = AdminController::$currentIndex;
        $helperList->token = $this->token;
        $helperList->module = $this->module;
        $helperList->no_link = true;
        $helperList->listTotal = count($manufacturers);

        return $helperList->generateList(
            $this->paginate($manufacturers),
            $fieldsList
        );
    }

    /**
     * @return bool
     */
    public function processStatus(): bool
    {
        $updateStatus = ManufacturerStatus::updateStatus((int) Tools::getValue('id'));

        $this->setTecDocAdminRedirectAfter(5);

        return $updateStatus;
    }
}
