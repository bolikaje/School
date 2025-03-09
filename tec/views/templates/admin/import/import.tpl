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

<div class="tecdoc-import-body" data-id="{$import->id}" data-rows-to-skip="{$import->rows_to_skip}" data-limit="{$per_page}" data-imported="0" data-total-rows="{$total_rows}" data-import-url="{$link->getAdminLink('AdminTecDocImport')}">
	<div class="panel">
		<div class="panel-heading">
			<i class="icon-cog"></i> {l s='Step 3 - Import' d='Modules.Itptecdoc.Admin'}
		</div>
		<div class="panel-body">
			<div class="import-alerts">
				<div class="alert alert-success import-alert import-alert-success hidden" role="alert">{l s='Import successfuly completed!' d='Modules.Itptecdoc.Admin'}</div>
				<div class="alert alert-danger import-alert import-alert-error hidden" role="alert"></div>
				<div class="alert alert-info import-alert import-alert-info hidden" role="alert"></div>
			</div>
			<div class="import-stats">
				<div class="import-stat">
					<h4 class="import-stat-value total-records">{$total_rows}</h4>
					<div class="import-stat-name">
                        {l s='Records found' d='Modules.Itptecdoc.Admin'}
					</div>
				</div>
				<div class="import-stat total-records-processed">
					<h4 class="import-stat-value">0</h4>
					<div class="import-stat-name">
                        {l s='Records proccesed' d='Modules.Itptecdoc.Admin'}
					</div>
				</div>
				<div class="import-stat total-truncated-records">
					<h4 class="import-stat-value">0</h4>
					<div class="import-stat-name">
                        {l s='Truncated records' d='Modules.Itptecdoc.Admin'}
					</div>
				</div>
				<div class="import-stat total-warnings">
					<h4 class="import-stat-value">0</h4>
					<div class="import-stat-name">
                        {l s='Warnings' d='Modules.Itptecdoc.Admin'}
					</div>
				</div>
				<div class="import-stat total-errors">
					<h4 class="import-stat-value">0</h4>
					<div class="import-stat-name">
                        {l s='Errors' d='Modules.Itptecdoc.Admin'}
					</div>
				</div>
			</div>
			<div class="import-progress hidden">
				<div class="import-progress-bar progress-bar " role="progressbar" aria-valuenow="1" aria-valuemin="0" aria-valuemax="100">
					0%
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<a href="{$mapping_link}" class="btn btn-default">{l s='Previous step' d='Modules.Itptecdoc.Admin'}</a>
			<a href="#" class="import-start btn btn-primary pull-right">{l s='Start import' d='Modules.Itptecdoc.Admin'}</a>
		</div>
	</div>
</div>