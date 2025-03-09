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

namespace ItPremium\TecDoc\Extensions\SupplierModule;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SupplierModuleHelper
{
    /**
     * This could be beneficial for suppliers who store files on their FTP server.
     *
     * @param string $ftpHost
     * @param string $ftpLogin
     * @param string $ftpPassword
     * @param string $downloadDirectory
     * @param int $ftpPort
     *
     * @return bool
     */
    public static function downloadFilesFromFTP(string $ftpHost, string $ftpLogin, string $ftpPassword, string $downloadDirectory, int $ftpPort = 21): bool
    {
        if (!function_exists('ftp_connect')) {
            return false;
        }

        $ftpConnection = @ftp_connect($ftpHost, $ftpPort);

        if (!$ftpConnection) {
            return false;
        }

        if (@ftp_login($ftpConnection, $ftpLogin, $ftpPassword)) {
            ftp_pasv($ftpConnection, true);

            if ($ftpFiles = ftp_nlist($ftpConnection, '.')) {
                foreach ($ftpFiles as $ftpFile) {
                    if (ftp_size($ftpConnection, $ftpFile) != -1) {
                        ftp_get($ftpConnection, $downloadDirectory . $ftpFile, $ftpFile);
                    }
                }
            } else {
                return false;
            }
        } else {
            return false;
        }

        ftp_close($ftpConnection);

        return true;
    }
}
