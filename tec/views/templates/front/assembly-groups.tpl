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

{extends file='module:itp_tecdoc/views/templates/front/layout.tpl'}

{block name='tecdoc_page_title'}
    {l s='Choose group' d='Modules.Itptecdoc.Shop'}
{/block}

{block name='tecdoc_page_subtitle'}
    {l s='Choose suitable group for %s %s %s' sprintf=[$manufacturer->name, $model_series->name, $vehicle->description] d='Modules.Itptecdoc.Shop'}
{/block}

{if $show_manufacturers_logo and $manufacturer->getImage(false)}
    {block name='tecdoc_page_title_after'}
        <img class="tecdoc-page-header__manufacturer-image" src="{$manufacturer->getImage(false)}" alt="{$manufacturer->name}"/>
    {/block}
{/if}

{block name='tecdoc_page_content'}
    <div class="tecdoc-assembly-groups">
        {include file='module:itp_tecdoc/views/templates/front/components/vehicle.tpl'}
        {include file='module:itp_tecdoc/views/templates/front/components/assembly-groups.tpl' assembly_groups=$assembly_groups}
    </div>
{/block}