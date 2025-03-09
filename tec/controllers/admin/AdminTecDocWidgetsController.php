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
use ItPremium\TecDoc\Entity\Widget;
use ItPremium\TecDoc\Enum\Orientation;
use ItPremium\TecDoc\Enum\WidgetType;
use ItPremium\TecDoc\Model\Data\AssemblyGroup;
use ItPremium\TecDoc\Model\Data\Brand;
use ItPremium\TecDoc\Model\Data\Manufacturer;
use ItPremium\TecDoc\Utils\Helper;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminTecDocWidgetsController extends TecDocAdminController
{
    /**
     * AdminTecDocWidgetsController constructor.
     *
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->className = Widget::class;
        $this->table = Widget::$definition['table'];
        $this->identifier = Widget::$definition['primary'];
        $this->bootstrap = true;
        $this->lang = true;
        $this->_defaultOrderBy = 'a.position';
        $this->position_identifier = 'a.position';

        parent::__construct();

        Shop::addTableAssociation($this->table, [
            'type' => 'shop',
        ]);

        $this->fields_list = [
            'id_tecdoc_widget' => [
                'title' => $this->trans('ID', [], 'Modules.Itptecdoc.Admin'),
            ],
            'name' => [
                'title' => $this->trans('Name', [], 'Modules.Itptecdoc.Admin'),
                'filter_key' => 'a!name',
            ],
            'public_name' => [
                'title' => $this->trans('Public name', [], 'Modules.Itptecdoc.Admin'),
            ],
            'hook' => [
                'title' => $this->trans('Hook', [], 'Modules.Itptecdoc.Admin'),
                'filter_key' => 'h!name',
            ],
            'type' => [
                'title' => $this->trans('Type', [], 'Modules.Itptecdoc.Admin'),
                'callback' => 'getTypeName',
            ],
            'position' => [
                'title' => $this->trans('Position', [], 'Modules.Itptecdoc.Admin'),
                'filter_key' => 'a!position',
                'position' => 'position',
                'align' => 'center',
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

        $this->_select = 'h.`name` AS hook';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'hook` h ON (h.`id_hook` = a.`id_hook`)';

        return parent::renderList();
    }

    /**
     * @return ObjectModel|bool|void
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws PrestaShopObjectNotFoundException
     */
    public function postProcess()
    {
        if (Tools::isSubmit('submitAddtecdoc_widget')) {
            if ($this->loadObject(true)) {
                $_POST['assembly_groups'] = implode(',', Tools::getValue('assembly_groups', []));
                $_POST['manufacturers'] = implode(',', Tools::getValue('manufacturers', []));
                $_POST['brands'] = implode(',', Tools::getValue('brands', []));

                if ($hookId = (int) Tools::getValue('id_hook')) {
                    $hookName = Hook::getNameById($hookId);

                    if (!Hook::isModuleRegisteredOnHook($this->module, $hookName, $this->context->shop->id)) {
                        Hook::registerHook($this->module, $hookName);
                    }

                    $this->module->clearCache();
                }
            }
        }

        return parent::postProcess();
    }

    /**
     * @return string
     *
     * @throws SmartyException
     * @throws MappingError
     * @throws Doctrine\DBAL\Driver\Exception
     * @throws Doctrine\DBAL\Exception
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws Symfony\Component\Cache\Exception\CacheException
     */
    public function renderForm(): string
    {
        /** @var Widget $widget */
        if ($widget = $this->loadObject(true)) {
            if ($widget->assembly_groups) {
                $this->fields_value['assembly_groups[]'] = explode(',', $widget->assembly_groups);
            }

            if ($widget->manufacturers) {
                $this->fields_value['manufacturers[]'] = explode(',', $widget->manufacturers);
            }

            if ($widget->brands) {
                $this->fields_value['brands[]'] = explode(',', $widget->brands);
            }
        }

        $assemblyGroups = $this->tecdoc
            ->assemblyGroups()
            ->getAssemblyGroups()
            ->map(function (AssemblyGroup $assemblyGroup) {
                $assemblyGroup->name = $assemblyGroup->name . ' (' . $assemblyGroup->type->label() . ')';

                return $assemblyGroup->toArray();
            })->toArray();

        $manufacturers = $this->tecdoc
            ->manufacturers()
            ->getManufacturers()
            ->map(function (Manufacturer $manufacturer) {
                return $manufacturer->toArray();
            })->toArray();

        $brands = $this->tecdoc
            ->brands()
            ->getBrands()
            ->map(function (Brand $brand) {
                return $brand->toArray();
            })->toArray();

        $this->fields_form = [
            'legend' => [
                'title' => $this->trans('Widget', [], 'Modules.Itptecdoc.Admin'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->trans('Name', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('The internal name for this widget.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'name',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Public name', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('The public name for this widget, may be visible on the front office.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'public_name',
                    'lang' => true,
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Hook', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'id_hook',
                    'required' => true,
                    'options' => [
                        'query' => $this->tecdoc->widgets()->getAvailableHooks(),
                        'id' => 'id_hook',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Type', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'type',
                    'required' => true,
                    'options' => [
                        'query' => Helper::prepareArrayForSelect(WidgetType::labels()),
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Orientation', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'orientation',
                    'options' => [
                        'query' => Helper::prepareArrayForSelect(Orientation::labels()),
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Show linkage target types', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Show options for linkage target types, such as passenger vehicles, commercial vehicles or motorcycles. If hidden, then first available linkage target type will be used as the default.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'show_linkage_target_types',
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
                    'type' => 'select',
                    'label' => $this->trans('Assembly groups', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'assembly_groups[]',
                    'desc' => $this->trans('Choose assembly groups to display in this widget. Hold CTRL to select multiple.', [], 'Modules.Itptecdoc.Admin'),
                    'multiple' => true,
                    'options' => [
                        'query' => $assemblyGroups,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Manufactures', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'manufacturers[]',
                    'desc' => $this->trans('Choose manufacturers to display in this widget. Hold CTRL to select multiple.', [], 'Modules.Itptecdoc.Admin'),
                    'multiple' => true,
                    'options' => [
                        'query' => $manufacturers,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Brands', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'brands[]',
                    'desc' => $this->trans('Choose brands to display in this widget. Hold CTRL to select multiple.', [], 'Modules.Itptecdoc.Admin'),
                    'multiple' => true,
                    'options' => [
                        'query' => $brands,
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'autoload_rte' => true,
                    'type' => 'textarea',
                    'name' => 'custom_html',
                    'label' => $this->trans('Custom HTML content', [], 'Modules.Itptecdoc.Admin'),
                    'lang' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Custom ID', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Custom ID for the widget container.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'custom_id',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Custom CSS class', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('CSS style for the widget container.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'custom_css_class',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Show public name', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Show public name on the front office.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'show_public_name',
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
                [
                    'type' => 'shop',
                    'label' => $this->trans('Shop association', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'checkBoxShopAsso',
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
     * @return void
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function ajaxProcessUpdatePositions(): void
    {
        $positions = Tools::getValue($this->table);
        $tecdocWidgetId = (int) Tools::getValue('id');
        $way = (int) Tools::getValue('way');

        foreach ($positions as $position => $value) {
            $pos = explode('_', $value);

            if (isset($pos[2]) and (int) $pos[2] === $tecdocWidgetId) {
                if ($widget = new Widget((int) $pos[2])) {
                    if (isset($position) and $widget->updatePosition($way, $position)) {
                        echo 'ok position ' . (int) $position . ' for widget ' . (int) $pos[1] . '\r\n';
                    } else {
                        echo '{"hasError" : true, "errors" : "Can not update widget ' . (int) $tecdocWidgetId . ' to position ' . (int) $position . ' "}';
                    }
                } else {
                    echo '{"hasError" : true, "errors" : "This widget (' . (int) $tecdocWidgetId . ') can t be loaded"}';
                }

                break;
            }
        }
    }

    /**
     * @param int $type
     *
     * @return string
     */
    public function getTypeName(int $type): string
    {
        return WidgetType::from($type)->label();
    }
}
