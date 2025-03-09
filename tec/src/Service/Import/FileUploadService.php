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

namespace ItPremium\TecDoc\Service\Import;

use ItPremium\TecDoc\Entity\Import;
use ItPremium\TecDoc\Enum\ImportMethod;
use ItPremium\TecDoc\Utils\Helper;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Csv;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class FileUploadService extends ImportService
{
    /**
     * @var array
     */
    private array $supportedExtensions = [
        'csv',
        'xml',
        'xls',
        'xlsx',
        'ods',
    ];

    /**
     * @param Import $import
     * @param array $file
     *
     * @return string|bool
     */
    public function uploadImportFile(Import $import, array $file): string|bool
    {
        if (!$this->validateImportFileExtension($import)) {
            $this->addError('File extension is invalid. Allowed extensions - %s.', [implode(', ', $this->supportedExtensions)]);

            return false;
        }

        $this->setDefaultSeparator($import->separator);

        $tmpFile = $this->uploadTmpFile($import, $file);

        $convertedFile = false;

        if ($tmpFile) {
            if (!Helper::validateFile($tmpFile)) {
                $this->addError('File is either empty or invalid.');
            } elseif (!$this->validateFileEncoding($tmpFile)) {
                $this->addError('File encoding is invalid. Only UTF-8 encoding is allowed.');
            } else {
                $convertedFile = $this->convertTmpFile($import, $tmpFile);
            }

            @unlink($tmpFile);
        }

        if (!$convertedFile) {
            $this->addError('No file was uploaded.');
        }

        return $convertedFile;
    }

    /**
     * @param Import $import
     * @param array $file
     *
     * @return string|bool
     */
    private function uploadTmpFile(Import $import, array $file): string|bool
    {
        return match (ImportMethod::from((int) $import->method)) {
            ImportMethod::FILE_UPLOAD => $this->uploadFile($file),
            ImportMethod::DOWNLOAD_FROM_URL => $this->downloadFileFromUrl($import->file_url),
            ImportMethod::DOWNLOAD_FROM_FTP => $this->downloadFileFromFtp($import->ftp_host, (int) $import->ftp_port, $import->ftp_username, $import->ftp_password, $import->ftp_file),
        };
    }

    /**
     * @param array $file
     *
     * @return string|bool
     */
    private function uploadFile(array $file): string|bool
    {
        if (!empty($file['error'])) {
            if ($file['error'] == UPLOAD_ERR_INI_SIZE) {
                $this->addError('The uploaded file exceeds the upload_max_filesize directive in php.ini. If your server configuration allows it, you may add a directive in your .htaccess.');
            } elseif ($file['error'] == UPLOAD_ERR_FORM_SIZE) {
                $this->addError('The uploaded file exceeds the post_max_size directive in php.ini. If your server configuration allows it, you may add a directive in your .htaccess.');
            } elseif ($file['error'] == UPLOAD_ERR_PARTIAL) {
                $this->addError('The uploaded file was only partially uploaded.');
            } elseif ($file['error'] == UPLOAD_ERR_NO_FILE) {
                $this->addError('No file was uploaded.');
            }

            return false;
        }

        $tmpFile = $this->getTmpFilePath($file['name']);

        if (!@move_uploaded_file($file['tmp_name'], $tmpFile)) {
            $this->addError('An error occurred while uploading the file.');

            return false;
        }

        return $tmpFile;
    }

    /**
     * @param string $fileUrl
     *
     * @return string|bool
     */
    private function downloadFileFromUrl(string $fileUrl): string|bool
    {
        if (!Helper::validateUrl($fileUrl)) {
            $this->addError('Invalid url.');

            return false;
        }

        $tmpFile = $this->getTmpFilePath(basename($fileUrl));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 6.1; Win64; x64; rv:84.0) Gecko/20100101 Firefox/84.0');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $fileUrl);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);

        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response and $responseCode == 200) {
            if (!@file_put_contents($tmpFile, $response)) {
                $this->addError('An error occurred while saving the file.');

                return false;
            }
        } else {
            $this->addError('File not found or it is invalid.');

            return false;
        }

        return $tmpFile;
    }

    /**
     * @param string $ftpHost
     * @param int $ftpPort
     * @param string $ftpUsername
     * @param string $ftpPassword
     * @param string $ftpFile
     *
     * @return string|bool
     */
    private function downloadFileFromFtp(string $ftpHost, int $ftpPort, string $ftpUsername, string $ftpPassword, string $ftpFile): string|bool
    {
        if (!$ftpHost or !$ftpPort or !$ftpUsername or !$ftpPassword or !$ftpFile) {
            $this->addError('Missing FTP credentials.');

            return false;
        }

        if (!function_exists('ftp_connect')) {
            $this->addError('Function ftp_connect not found. You need to enable it on your server.');

            return false;
        }

        $ftpConnection = @ftp_connect($ftpHost, $ftpPort);

        if (!$ftpConnection) {
            $this->addError('Could not connect to FTP server.');

            return false;
        }

        $ftpLogin = @ftp_login($ftpConnection, $ftpUsername, $ftpPassword);

        if (!$ftpLogin) {
            ftp_close($ftpConnection);

            if (function_exists('ftp_ssl_connect')) {
                $ftpConnection = @ftp_ssl_connect($ftpHost, $ftpPort);

                if (!$ftpConnection) {
                    $this->addError('Could not connect to FTP server.');

                    return false;
                }

                $ftpLogin = @ftp_login($ftpConnection, $ftpUsername, $ftpPassword);

                if (!$ftpLogin) {
                    ftp_close($ftpConnection);

                    $this->addError('Login to FTP server failed.');

                    return false;
                }
            } else {
                $this->addError('Login to FTP server failed.');

                return false;
            }
        }

        $tmpFile = $this->getTmpFilePath(basename($ftpFile));

        ftp_pasv($ftpConnection, true);

        if (!@ftp_get($ftpConnection, $tmpFile, $ftpFile)) {
            ftp_pasv($ftpConnection, false);

            if (!@ftp_get($ftpConnection, $tmpFile, $ftpFile)) {
                ftp_close($ftpConnection);

                $this->addError('Error downloading file from FTP.');

                return false;
            }
        }

        ftp_close($ftpConnection);

        return $tmpFile;
    }

    /**
     * @param Import $import
     * @param string $tmpFilePath
     *
     * @return string|bool
     */
    public function convertTmpFile(Import $import, string $tmpFilePath): string|bool
    {
        $tmpFileExtension = Helper::getFileExtension($tmpFilePath);

        return match ($tmpFileExtension) {
            'csv' => $this->convertCsvFile($tmpFilePath),
            'xml' => $this->convertXmlFile($tmpFilePath, $import->xml_path, $import->xml_nodes),
            'xls', 'xlsx', 'ods' => $this->convertExcelFile($tmpFilePath),
            default => false,
        };
    }

    /**
     * @param string $tmpFilePath
     *
     * @return string|bool
     */
    private function convertCsvFile(string $tmpFilePath): string|bool
    {
        $destinationFilePath = $this->generateFileName(true);

        return @copy($tmpFilePath, $destinationFilePath) ? $destinationFilePath : false;
    }

    /**
     * @param string $tmpFilePath
     * @param string $xmlPath
     * @param string $xmlNodes
     *
     * @return string|bool
     */
    private function convertXmlFile(string $tmpFilePath, string $xmlPath, string $xmlNodes): string|bool
    {
        $xmlFile = simplexml_load_file($tmpFilePath);

        $xmlNodes = explode(',', $xmlNodes);

        if (!$xmlFile or !$xmlPath or empty($xmlNodes)) {
            $this->addError('XML configuration is invalid.');

            return false;
        }

        $xmlElements = $xmlFile->xpath($xmlPath);

        if (empty($xmlElements)) {
            $this->addError('XML elements not found.');

            return false;
        }

        $destinationFilePath = $this->generateFileName(true);

        $csvFile = fopen($destinationFilePath, 'a');

        /**
         * Add headers
         */
        $headers = [];

        foreach ($xmlNodes as $xmlNode) {
            if (!empty($xmlElements[0]->xpath($xmlNode))) {
                $headers[] = $xmlNode;
            }
        }

        fputcsv($csvFile, $headers, $this->getDefaultSeparator());

        /*
         * Append body
         */
        foreach ($xmlElements as $xmlElement) {
            $values = [];

            foreach ($xmlNodes as $xmlNode) {
                if (!empty($xmlElement->xpath($xmlNode))) {
                    $values[] = (string) $xmlElement->xpath($xmlNode)[0];
                }
            }

            fputcsv($csvFile, $values, $this->getDefaultSeparator());
        }

        fclose($csvFile);

        return $destinationFilePath;
    }

    /**
     * @param string $tmpFilePath
     *
     * @return string|bool
     */
    private function convertExcelFile(string $tmpFilePath): string|bool
    {
        $destinationFilePath = $this->generateFileName(true);

        $spreadsheet = IOFactory::load($tmpFilePath);

        $writer = new Csv($spreadsheet);
        $writer->setDelimiter($this->getDefaultSeparator());
        $writer->save($destinationFilePath);

        return $destinationFilePath;
    }

    /**
     * @param string $fileName
     *
     * @return bool
     */
    public function deleteImportFile(string $fileName): bool
    {
        return @unlink($this->getFilePath($fileName));
    }

    /**
     * @param bool $withPath
     *
     * @return string
     */
    private function generateFileName(bool $withPath = false): string
    {
        $fileName = date('dmy-His') . '.csv';

        return $withPath ? $this->getFilePath($fileName) : $fileName;
    }

    /**
     * @param Import $import
     *
     * @return bool
     */
    public function validateImportFileExtension(Import $import): bool
    {
        return match (ImportMethod::from((int) $import->method)) {
            ImportMethod::FILE_UPLOAD => $this->validateFileExtension($import->file),
            ImportMethod::DOWNLOAD_FROM_URL => $this->validateFileExtension($import->file_url),
            ImportMethod::DOWNLOAD_FROM_FTP => $this->validateFileExtension($import->ftp_file),
        };
    }

    /**
     * @param $fileName
     *
     * @return bool
     */
    public function validateFileExtension($fileName): bool
    {
        $fileExtension = Helper::getFileExtension($fileName);

        return in_array($fileExtension, $this->supportedExtensions);
    }

    /**
     * @param string $filePath
     *
     * @return bool
     */
    public function validateFileEncoding(string $filePath): bool
    {
        return mb_check_encoding(\Tools::file_get_contents($filePath), 'UTF-8');
    }

    /**
     * @param string $file
     *
     * @return string
     */
    public function getTmpFilePath(string $file = ''): string
    {
        return $this->getFilePath('tmp' . DIRECTORY_SEPARATOR . $file);
    }

    /**
     * @return array
     */
    public function getSupportedExtensions(): array
    {
        return $this->supportedExtensions;
    }
}
