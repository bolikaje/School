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

<form class="tecdoc-search-form" method="GET" action="{$link->getModuleLink('itp_tecdoc', 'search', [], true)}">
	<div class="tecdoc-search-form__input-wrapper">
		<input type="text" class="tecdoc-search-form__input" value="{$search_query}" name="search_query" placeholder="{l s='Search...' d='Modules.Itptecdoc.Shop'}" title="{l s='Search...' d='Modules.Itptecdoc.Shop'}" required>
	</div>

	<div class="tecdoc-search-form__search-type">
		<div class="tecdoc-dropdown" x-data="tecdocDropdown({$search_type->value})" x-bind="trigger">
			<div class="tecdoc-dropdown__preview" x-ref="preview" x-text="dropdownPreview">{$search_type->label()}</div>

			<div class="tecdoc-dropdown__body" x-show="expanded" x-anchor.bottom-end.offset.10="$refs.preview" x-cloak x-transition>
				{foreach from=$search_type->labels() key=$search_type_key item=$search_type}
					<div class="tecdoc-dropdown__option" @click="selectOption({$search_type_key})">{$search_type}</div>
				{/foreach}
			</div>

			<input type="hidden" name="search_type" x-model="dropdownValue">
		</div>
	</div>

	<button type="submit" class="tecdoc-search-form__submit" title="{l s='Search' d='Modules.Itptecdoc.Shop'}"></button>
</form>