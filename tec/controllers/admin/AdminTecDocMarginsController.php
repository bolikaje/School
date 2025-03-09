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

use ItPremium\TecDoc\Constant\DatabaseConstant;
use ItPremium\TecDoc\Entity\Margin;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminTecDocMarginsController extends ModuleAdminController
{
    /**
     * AdminTecDocMarginsController constructor.
     *
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->className = Margin::class;
        $this->table = Margin::$definition['table'];
        $this->identifier = Margin::$definition['primary'];
        $this->bootstrap = true;

        parent::__construct();

        $this->fields_list = [
            'id_tecdoc_margin' => [
                'title' => $this->trans('ID', [], 'Modules.Itptecdoc.Admin'),
            ],
            'supplier' => [
                'title' => $this->trans('Supplier', [], 'Modules.Itptecdoc.Admin'),
                'filter_key' => 'ts!name',
            ],
            'brand' => [
                'title' => $this->trans('Brand', [], 'Modules.Itptecdoc.Admin'),
            ],
            'margin' => [
                'title' => $this->trans('Margin', [], 'Modules.Itptecdoc.Admin'),
            ],
            'price_range_start' => [
                'title' => $this->trans('Price range start', [], 'Modules.Itptecdoc.Admin'),
            ],
            'price_range_end' => [
                'title' => $this->trans('Price range end', [], 'Modules.Itptecdoc.Admin'),
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
     * @return bool|string
     *
     * @throws PrestaShopException
     */
    public function renderList(): bool|string
    {
        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->_select = 'ts.`name` AS supplier';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . DatabaseConstant::TECDOC_SUPPLIER_TABLE . '` ts ON (ts.`id_tecdoc_supplier` = a.`id_tecdoc_supplier`)';

        return parent::renderList();
    }

    /**
     * @return string
     *
     * @throws SmartyException
     * @throws Exception
     */
    public function renderForm(): string
    {
        $supplierRepository = $this->get('it_premium.tecdoc.repository.supplier');

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Margin', [], 'Modules.Itptecdoc.Admin'),
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->trans('Supplier', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('To create suppliers, please navigate to the relevant Suppliers section in the TecDoc module menu.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'id_tecdoc_supplier',
                    'required' => true,
                    'options' => [
                        'query' => $supplierRepository->getSuppliers(),
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Brand', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'brand',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Margin', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'margin',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Price range start', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'price_range_start',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Price range end', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'price_range_end',
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
