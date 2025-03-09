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

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use ItPremium\TecDoc\Entity\Import;
use ItPremium\TecDoc\Enum\ImportEntity;
use ItPremium\TecDoc\Enum\ImportMethod;
use ItPremium\TecDoc\Enum\ImportStatus;
use ItPremium\TecDoc\Service\Import\ImportService;
use ItPremium\TecDoc\Utils\Helper;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminTecDocImportController extends TecDocAdminController
{
    /**
     * @var ImportService
     */
    private ImportService $tecdocImport;

    /**
     * AdminTecDocImportController constructor.
     *
     * @throws PrestaShopException
     */
    public function __construct()
    {
        $this->className = Import::class;
        $this->table = Import::$definition['table'];
        $this->identifier = Import::$definition['primary'];
        $this->bootstrap = true;

        $this->addRowAction('edit');
        $this->addRowAction('import');
        $this->addRowAction('mapping');
        $this->addRowAction('delete');

        parent::__construct();

        $this->fields_list = [
            'id_tecdoc_import' => [
                'title' => $this->trans('ID', [], 'Modules.Itptecdoc.Admin'),
            ],
            'entity' => [
                'title' => $this->trans('Import entity', [], 'Modules.Itptecdoc.Admin'),
                'callback' => 'getEntityLabel',
            ],
            'method' => [
                'title' => $this->trans('Import method', [], 'Modules.Itptecdoc.Admin'),
                'callback' => 'getMethodLabel',
            ],
            'truncate_records' => [
                'title' => $this->trans('Truncate records', [], 'Modules.Itptecdoc.Admin'),
                'type' => 'bool',
                'align' => 'center',
            ],
            'status' => [
                'title' => $this->trans('Status', [], 'Modules.Itptecdoc.Admin'),
                'callback' => 'getStatusLabel',
            ],
            'date_import' => [
                'title' => $this->trans('Latest import at', [], 'Modules.Itptecdoc.Admin'),
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
     * @throws SmartyException
     */
    public function initContent(): void
    {
        parent::initContent();

        if (!$this->ajax) {
            if ($this->display == 'mapping') {
                $this->content = $this->renderMapping();
            }

            if ($this->display == 'import') {
                $this->content = $this->renderImport();
            }
        }

        $this->context->smarty->assign([
            'content' => $this->content,
        ]);
    }

    /**
     * @return void
     *
     * @throws Exception
     */
    public function initProcess(): void
    {
        parent::initProcess();

        $this->tecdocImport = $this->get('it_premium.tecdoc.service.import');

        if (Tools::getIsset('mapping' . $this->table)) {
            $this->display = 'mapping';
        }

        if (Tools::getIsset('import' . $this->table)) {
            $this->display = 'import';
        }
    }

    /**
     * @return void
     *
     * @throws PrestaShopException
     */
    public function postProcess(): void
    {
        parent::postProcess();

        if (Tools::isSubmit('submitAdd' . $this->table . 'AndPreview') and $this->object and !$this->errors) {
            $this->redirect_after = $this->getMappingLink((int) $this->object->id);
        }

        if (Tools::isSubmit('submitMappingHeaderRow' . $this->table)) {
            $this->postProcessImportHeaderRow();
        }

        if (Tools::isSubmit('submitMapping' . $this->table)) {
            $this->postProcessImportColumnMapping();

            if (!$this->errors) {
                $this->redirect_after = $this->getImportLink((int) $this->object->id);
            }
        }
    }

    /**
     * @return false|ObjectModel|void|null
     *
     * @throws Exception
     */
    public function processSave()
    {
        $importMethod = ImportMethod::tryFrom((int) Tools::getValue('method'));

        if ($xmlPath = Tools::getValue('xml_path')) {
            $_POST['xml_path'] = str_replace(' ', '', $xmlPath);
        }

        if ($xmlNodes = Tools::getValue('xml_nodes')) {
            $_POST['xml_nodes'] = str_replace(' ', '', $xmlNodes);
        }

        if (Tools::getIsset('file') and !Tools::getValue('file') and $importMethod == ImportMethod::FILE_UPLOAD) {
            unset($_POST['file']);
        } else {
            if ($filePath = $this->processFileUpload()) {
                $_POST['file'] = basename($filePath);
                $_POST['column_mapping'] = null;
                $_POST['default_values'] = null;
            }
        }

        return parent::processSave();
    }

    /**
     * @param bool $isNewTheme
     *
     * @return void
     */
    public function setMedia($isNewTheme = false): void
    {
        parent::setMedia($isNewTheme);

        $this->addJs($this->module->getPathUri() . '/views/js/import.js');
    }

    /**
     * @return string|bool
     *
     * @throws Exception
     */
    private function processFileUpload(): string|bool
    {
        $import = $this->loadObject(true);

        $fileUploadService = $this->get('it_premium.tecdoc.service.import.file_upload');

        if ($import->file) {
            $fileUploadService->deleteImportFile($import->file);
        }

        $this->copyFromPost($import, $this->table);

        if (!$file = $fileUploadService->uploadImportFile($import, $_FILES['file'])) {
            $this->errors = $fileUploadService->getErrors();
        }

        return $file;
    }

    /**
     * @return void
     */
    protected function _childValidation(): void
    {
        $importMethod = ImportMethod::tryFrom((int) Tools::getValue('method'));
        $importFile = Tools::getValue('file');
        $importFileUrl = Tools::getValue('file_url');
        $ftpHost = Tools::getValue('ftp_host');
        $ftpPort = Tools::getValue('ftp_port');
        $ftpUsername = Tools::getValue('ftp_username');
        $ftpPassword = Tools::getValue('ftp_password');
        $ftpFile = Tools::getValue('ftp_file');

        if ($importMethod == ImportMethod::FILE_UPLOAD) {
            $fileExists = $this->object ? $this->object->file : false;

            if (!$fileExists and !$importFile) {
                $this->errors[] = $this->trans('Please upload file.', [], 'Modules.Itptecdoc.Admin');
            }
        }

        if ($importMethod == ImportMethod::DOWNLOAD_FROM_URL) {
            if (!$importFileUrl) {
                $this->errors[] = $this->trans('File url is required.', [], 'Modules.Itptecdoc.Admin');
            }
        }

        if ($importMethod == ImportMethod::DOWNLOAD_FROM_FTP) {
            if (!$ftpHost) {
                $this->errors[] = $this->trans('FTP host is required.', [], 'Modules.Itptecdoc.Admin');
            }

            if (!$ftpPort) {
                $this->errors[] = $this->trans('FTP post is required.', [], 'Modules.Itptecdoc.Admin');
            }

            if (!$ftpUsername) {
                $this->errors[] = $this->trans('FTP username is required.', [], 'Modules.Itptecdoc.Admin');
            }

            if (!$ftpPassword) {
                $this->errors[] = $this->trans('FTP password is required.', [], 'Modules.Itptecdoc.Admin');
            }

            if (!$ftpFile) {
                $this->errors[] = $this->trans('FTP file url is required.', [], 'Modules.Itptecdoc.Admin');
            }
        }
    }

    /**
     * @return void
     *
     * @throws PrestaShopException
     */
    private function postProcessImportHeaderRow(): void
    {
        if ($import = $this->loadObject()) {
            $rowsToSkip = (int) substr(Tools::getValue('rows_to_skip', 0), 0, 6);

            $import->rows_to_skip = $rowsToSkip;
            $import->save();

            $_POST['rows_to_skip'] = $rowsToSkip;
        }
    }

    /**
     * @return void
     */
    private function postProcessImportColumnMapping(): void
    {
        $import = $this->loadObject(true);

        $mappedColumns = Tools::getValue('column', []);
        $defaultValues = Tools::getValue('default', []);

        if (!$this->tecdocImport->validateMapping($import->getAvailableMappingColumns(), $mappedColumns, $defaultValues)) {
            $this->errors[] = $this->trans('Please match each column of your source file to one of the destination columns!', [], 'Modules.Itptecdoc.Admin');
        }

        if (!$this->errors) {
            $import->setMappedColumns($mappedColumns);
            $import->setDefaultValues($defaultValues);
        }
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
                'title' => $this->trans('Step 1 - Import setup', [], 'Modules.Itptecdoc.Admin'),
                'icon' => 'icon-cogs',
            ],
            'input' => [
                [
                    'type' => 'select',
                    'label' => $this->trans('Import entity', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Choose which entity would you like to import.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'entity',
                    'required' => true,
                    'options' => [
                        'query' => Helper::prepareArrayForSelect(ImportEntity::labels()),
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Import method', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'method',
                    'required' => true,
                    'options' => [
                        'query' => Helper::prepareArrayForSelect(ImportMethod::labels()),
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'file',
                    'label' => $this->trans('Upload import file', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Allowed extensions: .csv, .xml, .xls, .xlsx, .ods. Only UTF-8 encoding is allowed.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'file',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('File url', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Specify full URL. E.g https://domain.com/example.csv.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'file_url',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('FTP host', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('URL should NOT be prefixed with ftp and should not have any trailing slashes. E.g ftp.domain.com', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'ftp_host',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('FTP port', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Default FTP port is set to 21.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'ftp_port',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('FTP username', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'ftp_username',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('FTP password', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'ftp_password',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('FTP file', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Path to FTP file.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'ftp_file',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Column separator', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'separator',
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('XML entity path', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Define path to iterable entity. E.g stock. Required for XML files.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'xml_path',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('XML nodes', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('XML nodes of iterable entity that represent attributes. Comma separated, nested elements allowed by slash. E.g price,availability/stock. Required for XML files.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'xml_nodes',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Reference suffix', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Reference suffix that must be stripped during import. E.g my_supplier_.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'reference_suffix',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Reference postfix', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Reference postfix that must be stripped during import. E.g _my_supplier.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'reference_postfix',
                ],
                [
                    'type' => 'switch',
                    'label' => $this->trans('Truncate all records', [], 'Modules.Itptecdoc.Admin'),
                    'desc' => $this->trans('Truncate all records for selected entity before starting an import.', [], 'Modules.Itptecdoc.Admin'),
                    'name' => 'truncate_records',
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
            'buttons' => [
                [
                    'type' => 'submit',
                    'name' => 'submitAdd' . $this->table . 'AndPreview',
                    'class' => 'pull-right',
                    'title' => $this->trans('Next step', [], 'Modules.Itptecdoc.Admin'),
                ],
            ],
            'submit' => [
                'title' => $this->trans('Save', [], 'Modules.Itptecdoc.Admin'),
                'class' => 'btn btn-default',
            ],
        ];

        $import = $this->loadObject(true);

        if (!$import->id) {
            $this->fields_value['separator'] = $this->tecdocImport->getDefaultSeparator();
            $this->fields_value['ftp_port'] = 21;
        }

        return $this->renderSteps() . parent::renderForm();
    }

    /**
     * @return string
     *
     * @throws SmartyException
     * @throws Exception
     */
    public function renderMapping(): string
    {
        $import = $this->loadObject();

        if (!$import) {
            return '';
        }

        if (!$import->file) {
            $this->errors[] = $this->trans('Import file is missing!', [], 'Modules.Itptecdoc.Admin');

            return '';
        }

        if (!$this->tecdocImport->setImportFile($import->file, $import->separator)) {
            $this->errors[] = $this->trans('Import file cannot be loaded.', [], 'Modules.Itptecdoc.Admin');

            return '';
        }

        return $this->renderSteps() .
            $this->renderMappingHeaderForm() .
            $this->renderMappingForm();
    }

    /**
     * @return string
     */
    public function renderMappingHeaderForm(): string
    {
        $fieldsForm = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->trans('Step 2 - Mapping settings', [], 'Modules.Itptecdoc.Admin'),
                        'icon' => 'icon-cog',
                    ],
                    'input' => [
                        [
                            'type' => 'text',
                            'name' => 'rows_to_skip',
                            'label' => $this->trans('Rows to skip', [], 'Modules.Itptecdoc.Admin'),
                            'desc' => $this->trans('Define how many rows should be skipped during import process.', [], 'Modules.Itptecdoc.Admin'),
                            'required' => true,
                        ],
                    ],
                ],
            ],
        ];

        $helperForm = new HelperForm();
        $helperForm->submit_action = 'submitMappingHeaderRow' . $this->table;
        $helperForm->token = $this->token;
        $helperForm->fields_value = [
            'rows_to_skip' => Tools::getValue('rows_to_skip', $this->object->rows_to_skip),
        ];

        return $helperForm->generateForm($fieldsForm);
    }

    /**
     * @return string
     *
     * @throws Exception
     */
    public function renderMappingForm(): string
    {
        $csvHeaderArr = [];

        foreach ($this->tecdocImport->getCsvHeader(--$this->object->rows_to_skip) as $key => $csvHeader) {
            $csvHeaderArr[] = [
                'id' => $key,
                'value' => $csvHeader,
            ];
        }

        $inputs = [];

        foreach ($this->object->getAvailableMappingColumns() as $fieldKey => $field) {
            $inputs[] = [
                'type' => 'mapping_select',
                'name' => $fieldKey,
                'name_column' => 'column[' . $fieldKey . ']',
                'name_default' => 'default[' . $fieldKey . ']',
                'label' => $field['label'],
                'required' => $field['required'] ?? false,
                'options' => [
                    'query' => $csvHeaderArr,
                    'id' => 'id',
                    'name' => 'value',
                ],
            ];
        }

        $fieldsForm = [
            [
                'form' => [
                    'legend' => [
                        'title' => $this->trans('Column mapping', [], 'Modules.Itptecdoc.Admin'),
                        'icon' => 'icon-cog',
                    ],
                    'input' => $inputs,
                    'buttons' => [
                        [
                            'type' => 'submit',
                            'name' => 'submitAdd' . $this->table . 'AndPreview',
                            'href' => $this->context->link->getAdminLink('AdminTecDocImport', true, [], [
                                'update' . $this->table => true,
                                'id_tecdoc_import' => $this->object->id,
                            ]),
                            'title' => $this->trans('Previous Step', [], 'Modules.Itptecdoc.Admin'),
                        ],
                    ],
                    'submit' => [
                        'title' => $this->trans('Next step', [], 'Modules.Itptecdoc.Admin'),
                        'class' => 'btn btn-primary pull-right',
                    ],
                ],
            ],
        ];

        $helperForm = new HelperForm();
        $helperForm->submit_action = 'submitMapping' . $this->table;
        $helperForm->token = $this->token;
        $helperForm->override_folder = 'import/';
        $helperForm->module = $this->module;
        $helperForm->fields_value = $this->getMappingFieldsValue($fieldsForm);
        $helperForm->tpl_vars = [
            'suppliers' => $this->get('it_premium.tecdoc.repository.supplier')->getSuppliers(),
        ];

        return $helperForm->generateForm($fieldsForm);
    }

    /**
     * @return string
     *
     * @throws SmartyException
     * @throws Exception
     */
    public function renderImport(): string
    {
        /** @var Import $import */
        $import = $this->loadObject();

        if (!$import) {
            return '';
        }

        if (!$import->file) {
            $this->errors[] = $this->trans('Import file is missing.', [], 'Modules.Itptecdoc.Admin');

            return '';
        }

        if (!$this->tecdocImport->validateMapping($import->getAvailableMappingColumns(), $import->getMappedColumns(), $import->getDefaultValues())) {
            $this->errors[] = $this->trans('Column mapping is invalid.', [], 'Modules.Itptecdoc.Admin');

            return '';
        }

        if (!$this->tecdocImport->setImportFile($import->file, $import->separator)) {
            $this->errors[] = $this->trans('Import file cannot be loaded.', [], 'Modules.Itptecdoc.Admin');

            return '';
        }

        $this->context->smarty->assign([
            'import' => $import,
            'per_page' => $this->tecdocImport->getLimit(),
            'total_rows' => $this->tecdocImport->countCsvRows((int) $import->rows_to_skip),
            'mapping_link' => $this->getMappingLink($import->id),
        ]);

        return $this->renderSteps() . $this->createTemplate('import/import.tpl')->fetch();
    }

    /**
     * @return string
     *
     * @throws SmartyException
     */
    public function renderSteps(): string
    {
        $this->context->smarty->assign([
            'display' => $this->display,
        ]);

        return $this->createTemplate('import/import_steps.tpl')->fetch();
    }

    /**
     * @return void
     *
     * @throws PrestaShopException
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function ajaxProcessImportStart(): void
    {
        header('Content-Type: application/json');

        /** @var Import $import */
        if ($import = $this->loadObject()) {
            $this->tecdocImport->importStart($import);

            exit(json_encode([
                'truncated_records' => $this->tecdocImport->getTruncatedRecordsCount(),
            ]));
        }
    }

    /**
     * @throws PrestaShopException
     * @throws Doctrine\DBAL\Exception
     */
    public function ajaxProcessImportRecords(): void
    {
        header('Content-Type: application/json');

        /** @var Import $import */
        if ($import = $this->loadObject()) {
            $offset = (int) Tools::getValue('offset', 0);

            $this->tecdocImport->importRecords($import, $offset);

            exit(json_encode([
                'total_records' => $this->tecdocImport->getTotalRecordsCount(),
                'errors' => $this->tecdocImport->getErrors(),
                'warnings' => $this->tecdocImport->getWarnings(),
            ]));
        }
    }

    /**
     * @throws PrestaShopException
     */
    public function ajaxProcessUpdateImportStatus(): void
    {
        header('Content-Type: application/json');

        /** @var Import $import */
        if ($import = $this->loadObject()) {
            $importError = (bool) Tools::getValue('import_error', 0);

            $this->tecdocImport->updateImportStatus($import, $importError);

            exit(json_encode([
                'success' => true,
            ]));
        }
    }

    /**
     * @param array $mappingFieldsForm
     *
     * @return array
     */
    protected function getMappingFieldsValue(array $mappingFieldsForm): array
    {
        $mappingFieldsValues = [];

        /** @var Import $import */
        $import = $this->object;

        foreach ($mappingFieldsForm as $fieldset) {
            if (isset($fieldset['form']['input'])) {
                foreach ($fieldset['form']['input'] as $input) {
                    $mappingFieldsValues[$input['name_column']] = $this->getMappingFieldValue($import, $input['name']);
                    $mappingFieldsValues[$input['name_default']] = $this->getMappingFieldValue($import, $input['name'], true);
                }
            }
        }

        return $mappingFieldsValues;
    }

    /**
     * @param Import $import
     * @param string $key
     * @param bool $defaultValue
     *
     * @return string
     */
    protected function getMappingFieldValue(Import $import, string $key, bool $defaultValue = false): string
    {
        if ($defaultValue) {
            $dataColumn = $import->getDefaultValues();
            $param = 'default';
        } else {
            $dataColumn = $import->getMappedColumns();
            $param = 'column';
        }

        $defaultValue = $dataColumn[$key] ?? '';

        return $_POST[$param][$key] ?? $defaultValue;
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function getMappingLink(int $id): string
    {
        return $this->context->link->getAdminLink('AdminTecDocImport', true, [], [
            'id_tecdoc_import' => $id,
            'mapping' . $this->table => true,
        ]);
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function getImportLink(int $id): string
    {
        return $this->context->link->getAdminLink('AdminTecDocImport', true, [], [
            'id_tecdoc_import' => $id,
            'import' . $this->table => true,
        ]);
    }

    /**
     * @param $entity
     *
     * @return string
     */
    public function getEntityLabel($entity): string
    {
        return $this->trans(ImportEntity::tryFrom((int) $entity)->label(), [], 'Modules.Itptecdoc.Admin');
    }

    /**
     * @param $method
     *
     * @return string
     */
    public function getMethodLabel($method): string
    {
        return $this->trans(ImportMethod::tryFrom((int) $method)->label(), [], 'Modules.Itptecdoc.Admin');
    }

    /**
     * @param $status
     *
     * @return string
     */
    public function getStatusLabel($status): string
    {
        return $this->trans(ImportStatus::tryFrom((int) $status)->label(), [], 'Modules.Itptecdoc.Admin');
    }
}
