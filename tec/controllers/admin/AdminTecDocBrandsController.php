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
use ItPremium\TecDoc\Entity\BrandStatus;
use ItPremium\TecDoc\Enum\BrandQuality;
use ItPremium\TecDoc\Utils\Helper;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminTecDocBrandsController extends TecDocAdminController
{
    /**
     * AdminTecDocBrandsController constructor.
     *
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->table = BrandStatus::$definition['table'];
        $this->identifier = 'id';
        $this->bootstrap = true;

        parent::__construct();
    }

    /**
     * @return void
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws PrestaShopException
     * @throws TecDocApiException
     * @throws Doctrine\DBAL\Driver\Exception
     * @throws Doctrine\DBAL\Exception
     * @throws Symfony\Component\Cache\Exception\CacheException
     */
    public function initContent(): void
    {
        if ($this->display == 'edit' or $this->display == 'add') {
            $this->object = $this
                ->tecdoc
                ->brands()
                ->getBrandById((int) Tools::getValue($this->identifier), false);

            if (!$this->object) {
                $this->errors[] = $this->trans('The object cannot be loaded (or found).', [], 'Admin.Notifications.Error');

                return;
            }

            $this->content .= $this->renderForm();
        } elseif (!$this->ajax) {
            $this->content .= $this->renderList();
            $this->content .= $this->renderOptions();
        }

        $this->context->smarty->assign([
            'content' => $this->content,
        ]);
    }

    /**
     * @return string
     *
     * @throws MappingError
     * @throws Doctrine\DBAL\Driver\Exception
     * @throws Doctrine\DBAL\Exception
     * @throws GuzzleException
     * @throws TecDocApiException
     * @throws PrestaShopException
     * @throws Symfony\Component\Cache\Exception\CacheException
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
            'quality' => [
                'title' => $this->trans('Quality', [], 'Modules.Itptecdoc.Admin'),
                'search' => false,
                'callback' => 'getQualityLabel',
            ],
            'active' => [
                'title' => $this->trans('Active', [], 'Modules.Itptecdoc.Admin'),
                'active' => 'status',
                'type' => 'bool',
                'align' => 'center',
                'search' => false,
            ],
        ];

        $brands = $this->tecdoc
            ->brands()
            ->getBrands(false)
            ->map(function ($brand) {
                return $brand->toArray();
            })->toArray();

        $helperList = new HelperList();
        $helperList->title = $this->trans('Brands', [], 'Modules.Itptecdoc.Admin');
        $helperList->identifier = 'id';
        $helperList->table = $this->table;
        $helperList->currentIndex = AdminController::$currentIndex;
        $helperList->token = $this->token;
        $helperList->module = $this->module;
        $helperList->actions = ['edit'];
        $helperList->listTotal = count($brands);

        return $helperList->generateList(
            $this->paginate($brands),
            $fieldsList
        );
    }

    /**
     * @return string
     */
    public function renderForm(): string
    {
        $fieldsForm = [
            ['form' => [
                'legend' => [
                    'title' => $this->trans('Edit brand - %s', [$this->object->name], 'Modules.Itptecdoc.Admin'),
                ],
                'input' => [
                    [
                        'type' => 'select',
                        'label' => $this->trans('Brand quality', [], 'Modules.Itptecdoc.Admin'),
                        'name' => 'quality',
                        'options' => [
                            'query' => Helper::prepareArrayForSelect(BrandQuality::labels()),
                            'id' => 'id',
                            'name' => 'name',
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
            ]],
        ];

        $helperForm = new HelperForm();
        $helperForm->submit_action = 'submit' . $this->module->name;
        $helperForm->token = $this->token;

        $helperForm->fields_value['quality'] = BrandStatus::getQuality($this->object->id);
        $helperForm->fields_value['active'] = BrandStatus::getStatus($this->object->id);

        return $helperForm->generateForm($fieldsForm);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initProcess(): void
    {
        if (Tools::isSubmit('submit' . $this->module->name)) {
            $tecdocBrandId = (int) Tools::getValue('id');
            $quality = (int) Tools::getValue('quality');
            $active = (bool) Tools::getValue('active', true);

            if (BrandStatus::createOrUpdateByBrandId($tecdocBrandId, $quality, $active)) {
                $this->setTecDocAdminRedirectAfter(4);
            }
        }

        parent::initProcess();
    }

    /**
     * @return bool
     */
    public function processStatus(): bool
    {
        $updateStatus = BrandStatus::updateStatus((int) Tools::getValue('id'));

        $this->setTecDocAdminRedirectAfter(5);

        return $updateStatus;
    }

    /**
     * @param BrandQuality $quality
     *
     * @return string
     */
    public function getQualityLabel(BrandQuality $quality): string
    {
        return $quality->label();
    }
}
