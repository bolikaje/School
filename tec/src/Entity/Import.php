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

namespace ItPremium\TecDoc\Entity;

use ItPremium\TecDoc\Constant\DatabaseConstant;
use ItPremium\TecDoc\Enum\ImportEntity;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Import extends \ObjectModel
{
    /** @var int */
    public $id;

    /** @var int */
    public $entity;

    /** @var int */
    public $method;

    /** @var string */
    public $file;

    /** @var string */
    public $file_url;

    /** @var string */
    public $ftp_host;

    /** @var int */
    public $ftp_port;

    /** @var string */
    public $ftp_username;

    /** @var string */
    public $ftp_password;

    /** @var string */
    public $ftp_file;

    /** @var string */
    public $xml_path;

    /** @var string */
    public $xml_nodes;

    /** @var string */
    public $separator;

    /** @var string */
    public $reference_suffix;

    /** @var string */
    public $reference_postfix;

    /** @var bool */
    public $truncate_records;

    /** @var int */
    public $rows_to_skip;

    /** @var string */
    public $column_mapping;

    /** @var string */
    public $default_values;

    /** @var int */
    public $status;

    /** @var string */
    public $date_import;

    /** @var array */
    public static $definition = [
        'table' => DatabaseConstant::TECDOC_IMPORT_TABLE,
        'primary' => 'id_tecdoc_import',
        'fields' => [
            'entity' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 1, 'required' => true],
            'method' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 1, 'required' => true],
            'file' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'file_url' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'ftp_host' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'ftp_port' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 255],
            'ftp_username' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'ftp_password' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'ftp_file' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'xml_path' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'xml_nodes' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'separator' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 1, 'required' => true],
            'reference_suffix' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'reference_postfix' => ['type' => self::TYPE_STRING, 'validate' => 'isString', 'size' => 255],
            'truncate_records' => ['type' => self::TYPE_BOOL],
            'rows_to_skip' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 6],
            'column_mapping' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'default_values' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'status' => ['type' => self::TYPE_INT, 'validate' => 'isInt', 'size' => 1],
            'date_import' => ['type' => self::TYPE_DATE],
        ],
    ];

    /**
     * @param $id
     * @param $id_lang
     * @param $id_shop
     * @param $translator
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function __construct($id = null, $id_lang = null, $id_shop = null, $translator = null)
    {
        parent::__construct($id, $id_lang, $id_shop, $translator);
    }

    /**
     * @return bool
     *
     * @throws \PrestaShopException
     */
    public function delete()
    {
        if (!$result = parent::delete()) {
            return false;
        }

        if ($this->file) {
            @unlink(_PS_MODULE_DIR_ . 'itp_tecdoc/uploads/' . $this->file);
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getMappedColumns(): array
    {
        return $this->column_mapping ? json_decode($this->column_mapping, true) : [];
    }

    /**
     * @param $mappedColumns
     *
     * @return bool
     *
     * @throws \PrestaShopException
     * @throws \Exception
     */
    public function setMappedColumns($mappedColumns): bool
    {
        $this->column_mapping = $this->prepareMappingDataJson($mappedColumns);

        return $this->save();
    }

    /**
     * @return array
     */
    public function getDefaultValues(): array
    {
        return $this->default_values ? json_decode($this->default_values, true) : [];
    }

    /**
     * @param $defaultValues
     *
     * @return bool
     *
     * @throws \PrestaShopException
     * @throws \Exception
     */
    public function setDefaultValues($defaultValues): bool
    {
        $this->default_values = $this->prepareMappingDataJson($defaultValues);

        return $this->save();
    }

    /**
     * @return array
     *
     * @throws \Exception
     */
    public function getAvailableMappingColumns(): array
    {
        return match (ImportEntity::from((int) $this->entity)) {
            ImportEntity::STOCK => [
                'id_tecdoc_supplier' => [
                    'label' => $this->trans('Supplier', [], 'Modules.Itptecdoc.Admin'),
                    'required' => true,
                ],
                'brand' => [
                    'label' => $this->trans('Brand', [], 'Modules.Itptecdoc.Admin'),
                    'required' => true,
                ],
                'reference' => [
                    'label' => $this->trans('Reference', [], 'Modules.Itptecdoc.Admin'),
                    'required' => true,
                ],
                'name' => [
                    'label' => $this->trans('Name', [], 'Modules.Itptecdoc.Admin'),
                ],
                'wholesale_price' => [
                    'label' => $this->trans('Wholesale price', [], 'Modules.Itptecdoc.Admin'),
                ],
                'price' => [
                    'label' => $this->trans('Price', [], 'Modules.Itptecdoc.Admin'),
                    'required' => true,
                ],
                'deposit' => [
                    'label' => $this->trans('Deposit', [], 'Modules.Itptecdoc.Admin'),
                ],
                'minimum_order_quantity' => [
                    'label' => $this->trans('Minimum order quantity', [], 'Modules.Itptecdoc.Admin'),
                ],
                'enforce_quantity_multiple' => [
                    'label' => $this->trans('Enforce quantity multiple', [], 'Modules.Itptecdoc.Admin'),
                ],
                'stock' => [
                    'label' => $this->trans('Stock', [], 'Modules.Itptecdoc.Admin'),
                    'required' => true,
                ],
                'delivery_time' => [
                    'label' => $this->trans('Delivery time', [], 'Modules.Itptecdoc.Admin'),
                    'required' => true,
                ],
                'weight' => [
                    'label' => $this->trans('Weight', [], 'Modules.Itptecdoc.Admin'),
                ],
                'oem' => [
                    'label' => $this->trans('OEM', [], 'Modules.Itptecdoc.Admin'),
                ],
                'active' => [
                    'label' => $this->trans('Active', [], 'Modules.Itptecdoc.Admin'),
                    'required' => true,
                ],
            ],
            ImportEntity::SUPPLIER => [
                'name' => [
                    'label' => $this->trans('Name', [], 'Modules.Itptecdoc.Admin'),
                    'required' => true,
                ],
                'email' => [
                    'label' => $this->trans('Email', [], 'Modules.Itptecdoc.Admin'),
                ],
                'phone' => [
                    'label' => $this->trans('Phone', [], 'Modules.Itptecdoc.Admin'),
                ],
                'address' => [
                    'label' => $this->trans('Address', [], 'Modules.Itptecdoc.Admin'),
                ],
                'active' => [
                    'label' => $this->trans('Active', [], 'Modules.Itptecdoc.Admin'),
                ],
            ],
        };
    }

    /**
     * @param array $data
     *
     * @return string|bool
     *
     * @throws \Exception
     */
    private function prepareMappingDataJson(array $data): string|bool
    {
        $mappingData = [];

        foreach ($this->getAvailableMappingColumns() as $fieldKey => $field) {
            if (isset($data[$fieldKey])) {
                $mappingData[$fieldKey] = $data[$fieldKey];
            }
        }

        return json_encode($mappingData);
    }
}
