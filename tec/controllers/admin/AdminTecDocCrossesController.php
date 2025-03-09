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

use ItPremium\TecDoc\Entity\Cross;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminTecDocCrossesController extends ModuleAdminController
{
    /**
     * AdminTecDocCrossesController constructor.
     *
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->className = Cross::class;
        $this->table = Cross::$definition['table'];
        $this->identifier = Cross::$definition['primary'];
        $this->bootstrap = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();

        $this->fields_list = [
            'id_tecdoc_cross' => [
                'title' => $this->trans('ID', [], 'Modules.Itptecdoc.Admin'),
            ],
            'brand' => [
                'title' => $this->trans('Brand', [], 'Modules.Itptecdoc.Admin'),
            ],
            'reference' => [
                'title' => $this->trans('Reference', [], 'Modules.Itptecdoc.Admin'),
            ],
            'cross_brand' => [
                'title' => $this->trans('Cross brand', [], 'Modules.Itptecdoc.Admin'),
            ],
            'cross_reference' => [
                'title' => $this->trans('Cross reference', [], 'Modules.Itptecdoc.Admin'),
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
                'title' => $this->trans('Cross', [], 'Modules.Itptecdoc.Admin'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Brand', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'brand',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Reference', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'reference',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Cross brand', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'cross_brand',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Cross reference', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'cross_reference',
                    'required' => true,
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
