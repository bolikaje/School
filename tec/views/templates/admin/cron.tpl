{**
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
 *}

<div class="panel" id="cron-setup">
    <h3>
        <i class="icon icon-clock-o"></i> {l s='Scheduled tasks (CRON) setup' d='Modules.Itptecdoc.Admin'}
    </h3>
    <p>
        {l s='The module allows you to set up scheduled tasks for the supported actions listed below. You can use HTTP calls or the Command-Line Interface (CLI) to schedule these tasks.' d='Modules.Itptecdoc.Admin'}<br />
    </p>
    <br />
    <p>
        <strong>{l s='1. Clear API Cache (clear-api-cache)' d='Modules.Itptecdoc.Admin'}</strong>
    </p>
    <p>{l s='Please turn on the "Cache API responses" option in the TecDoc web service API settings section before creating a CRON job for this action.' d='Modules.Itptecdoc.Admin'}</p>
    <p>
        {l s='For HTTP - %s&action=%s' sprintf=[$cron_link, 'clear-api-cache'] d='Modules.Itptecdoc.Admin'}
    </p>
    <p>
        {l s='For Command-Line Interface - php %s --action=%s' sprintf=[$cron_cli_link, 'clear-api-cache'] d='Modules.Itptecdoc.Admin'}
    </p>
    <br />
    <p>
        <strong>{l s='2. Delete cached products (delete-cached-products)' d='Modules.Itptecdoc.Admin'}</strong>
    </p>
    <p>
        {l s='This module utilizes PrestaShop\'s database to cache data for TecDoc products, custom articles, and deposit products. Storing these products locally is essential for the cart system to function correctly. While these cached products are not visible anywhere, they do occupy space in your database.' d='Modules.Itptecdoc.Admin'}
    </p>
    <p>
        {l s='Products in carts that haven\'t been ordered will not be deleted.' d='Modules.Itptecdoc.Admin'}
    </p>
    <p>
        {l s='For HTTP - %s&action=%s' sprintf=[$cron_link, 'delete-cached-products'] d='Modules.Itptecdoc.Admin'}
    </p>
    <p>
        {l s='For Command-Line Interface - php %s --action=%s' sprintf=[$cron_cli_link, 'delete-cached-products'] d='Modules.Itptecdoc.Admin'}
    </p>
</div>