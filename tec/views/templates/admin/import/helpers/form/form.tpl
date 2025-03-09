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

{extends file='helpers/form/form.tpl'}

{block name='input' append}
    {if $input.type == 'mapping_select'}
		<div class="row">
			<div class="col-xs-6 col-md-4">
				<select name="{$input.name_column|escape:'html':'UTF-8'}" id="{$input.name_column|escape:'html':'UTF-8'}" title="{$input.label}">
					<option value="">{l s='Ignore this column' d='Modules.Itptecdoc.Admin'}</option>
                    {foreach from=$input.options.query item=$option}
						<option value="{$option[$input.options.id]}" {if isset($fields_value[$input.name_column]) && $fields_value[$input.name_column] == $option[$input.options.id]}selected="selected"{/if}>{$option[$input.options.name]}</option>
                    {/foreach}
				</select>
			</div>

			<div class="col-xs-6 col-md-4">
                {if $input.name == 'id_tecdoc_supplier'}
					<select name="{$input.name_default|escape:'html':'UTF-8'}" title="{$input.label}">
                        {foreach from=$suppliers item=$supplier}
							<option value="{$supplier.id}" {if isset($fields_value[$input.name_default]) && $fields_value[$input.name_default] == $supplier.id}selected="selected"{/if}>{$supplier.name}</option>
                        {/foreach}
					</select>
                {elseif $input.name == 'oem' or $input.name == 'active' or $input.name == 'enforce_quantity_multiple'}
					<select name="{$input.name_default|escape:'html':'UTF-8'}" title="{$input.label}">
						<option value="0" {if isset($fields_value[$input.name_default]) && $fields_value[$input.name_default] == 0}selected="selected"{/if}>{l s='No' d='Modules.Itptecdoc.Admin'}</option>
						<option value="1" {if isset($fields_value[$input.name_default]) && $fields_value[$input.name_default] == 1}selected="selected"{/if}>{l s='Yes' d='Modules.Itptecdoc.Admin'}</option>
					</select>
                {else}
					<input type="text" name="{$input.name_default|escape:'html':'UTF-8'}" value="{$fields_value[$input.name_default]|escape:'html':'UTF-8'}" title="{l s='Default value' d='Modules.Itptecdoc.Admin'}" placeholder="{l s='Default value' d='Modules.Itptecdoc.Admin'}">
                {/if}
			</div>
		</div>
    {/if}
{/block}