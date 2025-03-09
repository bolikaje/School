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

use ItPremium\TecDoc\Entity\Supplier;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminTecDocSuppliersController extends ModuleAdminController
{
    /**
     * AdminTecDocSuppliersController constructor.
     *
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->className = Supplier::class;
        $this->table = Supplier::$definition['table'];
        $this->identifier = Supplier::$definition['primary'];
        $this->bootstrap = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();

        $this->fields_list = [
            'id_tecdoc_supplier' => [
                'title' => $this->trans('ID', [], 'Modules.Itptecdoc.Admin'),
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Modules.Itptecdoc.Admin'),
            ],
            'email' => [
                'title' => $this->trans('Email', [], 'Modules.Itptecdoc.Admin'),
            ],
            'phone' => [
                'title' => $this->trans('Phone', [], 'Modules.Itptecdoc.Admin'),
            ],
            'address' => [
                'title' => $this->trans('Address', [], 'Modules.Itptecdoc.Admin'),
            ],
            'active' => [
                'title' => $this->trans('Active', [], 'Modules.Itptecdoc.Admin'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
            ],
        ];

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Modules.Itptecdoc.Admin'),
                'confirm' => $this->trans('Delete selected items?', [], 'Modules.Itptecdoc.Admin'),
            ],
        ];
    }

    /**
     * @return string
     *
     * @throws SmartyException
     */
    public function renderForm(): string
    {
        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Supplier', [], 'Modules.Itptecdoc.Admin'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'name',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Email', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'email',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Phone', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'phone',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Address', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'address',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Active', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'active',
                    'values' => [
                        [
                            'id' => 'active_on',
                            'value' => true,
                            'label' => $this->trans('Yes', [], 'Modules.Itptecdoc.Admin'),
                        ],
                        [
                            'id' => 'active_off',
                            'value' => false,
                            'label' => $this->trans('No', [], 'Modules.Itptecdoc.Admin'),
                        ],
                    ],
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Modules.Itptecdoc.Admin'),
                'class' => 'btn btn-default',
            ],
        ];

        return parent::renderForm();
    }
}
