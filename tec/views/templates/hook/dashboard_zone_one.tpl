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

<section id="itp_tecdoc" class="panel widget">
    <div class="panel-heading">
        <i class="icon-table"></i> {l s='TecDoc sales' d='Modules.Itptecdoc.Admin'}
        <span class="panel-heading-action">
			<a class="list-toolbar-btn" href="#" onclick="refreshDashboard('itp_tecdoc'); return false;" title="{l s='Refresh' d='Admin.Actions'}">
				<i class="process-icon-refresh"></i>
			</a>
		</span>
    </div>
    <ul class="data_list_large">
        <li>
			<span class="data_label size_l">
				{l s='Orders' d='Modules.Itptecdoc.Admin'}
				<small class="text-muted"><br/>
					{l s='Orders with TecDoc products' d='Modules.Itptecdoc.Admin'}
				</small>
			</span>
            <span class="data_value size_l">
				<span id="tecdoc_orders_count"></span>
			</span>
        </li>
		<li>
			<span class="data_label size_l">
				{l s='Revenue' d='Modules.Itptecdoc.Admin'}
				<small class="text-muted"><br/>
					{l s='Tax excluded' d='Modules.Itptecdoc.Admin'}
				</small>
			</span>
			<span class="data_value size_l">
				<span id="tecdoc_revenue_tax_excl"></span>
			</span>
		</li>
        <li>
			<span class="data_label size_l">
				{l s='Revenue' d='Modules.Itptecdoc.Admin'}
				<small class="text-muted"><br/>
					{l s='Tax included' d='Modules.Itptecdoc.Admin'}
				</small>
			</span>
            <span class="data_value size_l">
				<span id="tecdoc_revenue_tax_incl"></span>
			</span>
        </li>
    </ul>
</section>