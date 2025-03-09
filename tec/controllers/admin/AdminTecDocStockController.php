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

use ItPremium\TecDoc\Entity\Stock;
use ItPremium\TecDoc\Entity\Supplier;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminTecDocStockController extends ModuleAdminController
{
    /**
     * AdminTecDocStockController constructor.
     *
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->className = Stock::class;
        $this->table = Stock::$definition['table'];
        $this->identifier = Stock::$definition['primary'];
        $this->bootstrap = true;
        $this->_use_found_rows = false;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        parent::__construct();

        $this->fields_list = [
            'id_tecdoc_stock' => [
                'title' => $this->trans('ID', [], 'Modules.Itptecdoc.Admin'),
            ],
            'id_tecdoc_supplier' => [
                'title' => $this->trans('Supplier', [], 'Modules.Itptecdoc.Admin'),
                'callback' => 'getTecdocSupplierName',
                'search' => false,
            ],
            'brand' => [
                'title' => $this->trans('Brand', [], 'Modules.Itptecdoc.Admin'),
            ],
            'reference' => [
                'title' => $this->trans('Reference', [], 'Modules.Itptecdoc.Admin'),
            ],
            'wholesale_price' => [
                'title' => $this->trans('Wholesale price', [], 'Modules.Itptecdoc.Admin'),
            ],
            'price' => [
                'title' => $this->trans('Price', [], 'Modules.Itptecdoc.Admin'),
            ],
            'minimum_order_quantity' => [
                'title' => $this->trans('Minimum order quantity', [], 'Modules.Itptecdoc.Admin'),
            ],
            'stock' => [
                'title' => $this->trans('Stock', [], 'Modules.Itptecdoc.Admin'),
            ],
            'delivery_time' => [
                'title' => $this->trans('Delivery time', [], 'Modules.Itptecdoc.Admin'),
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
     * @throws Exception
     */
    public function renderForm(): string
    {
        $supplierRepository = $this->get('it_premium.tecdoc.repository.supplier');

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Stock', [], 'Modules.Itptecdoc.Admin'),
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
                    'label' => $this->trans('Name', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('If Custom Articles are enabled, this name can be used as the article name.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'name',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Wholesale price', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'wholesale_price',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Price', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Price without tax.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'price',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Deposit', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('If a deposit is required for this stock record, you can specify it here. Without tax.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'deposit',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Minimum order quantity', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Please specify the minimum quantity for ordering.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'minimum_order_quantity',
                    'required' => true,
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Enforce quantity multiple', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('When this option is enabled, the quantity of the product must be a multiple of the specified minimum order quantity. If this option is turned off, there are no such restrictions, and customers can order any amount exceeding the minimum quantity. For instance, if the minimum order quantity is set to 2 and this option is turned on, customers can only order in quantities of 2, 4, 6, and etc.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'enforce_quantity_multiple',
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
                [
                    'type' => 'text',
                    'label' => $this->trans('Stock', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'stock',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Delivery time', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Please specify the maximum delivery time in days.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'delivery_time',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Weight', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('This weight will be added to the PrestaShop product and can be used for future shipping calculations.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'weight',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('OEM', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'oem',
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

    /**
     * @return ObjectModel|false|void|null
     */
    public function processSave()
    {
        if ($_POST['minimum_order_quantity'] <= 0) {
            $_POST['minimum_order_quantity'] = 1;
        }

        return parent::processSave();
    }

    /**
     * @param int $supplierId
     *
     * @return string
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getTecdocSupplierName(int $supplierId): string
    {
        $tecdocSupplier = new Supplier($supplierId);

        return Validate::isLoadedObject($tecdocSupplier) ? $tecdocSupplier->name : '';
    }
}
