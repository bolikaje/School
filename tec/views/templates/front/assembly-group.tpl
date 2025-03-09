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

{if !$show_vehicle_search}
    {block name='tecdoc_page_title'}
        {$page.title}
    {/block}

    {block name='tecdoc_page_subtitle'}
        {l s='Find the spare parts you need quickly and easily' d='Modules.Itptecdoc.Shop'}
    {/block}
{else}
    {block name='tecdoc_page_header_container'}
        <section class="tecdoc-page__header tecdoc-page-header">
            <div class="tecdoc-page-header__column">
                {$vehicle_search_widget->renderTemplate() nofilter}
            </div>

            <div class="tecdoc-page-header__column tecdoc-page-header__column--full">
                {if $assembly_group->getImage(false)}
                    <img src="{$assembly_group->getImage()}" class="tecdoc-page-header__assembly-group-image" alt="{$page.title}" title="{$page.title}">
                {/if}

                <h3 class="tecdoc-page-header__heading tecdoc-heading">{$page.title}</h3>

                <div class="tecdoc-page-header__subheading">
                    {l s='Find the spare parts you need quickly and easily.' d='Modules.Itptecdoc.Shop'}
                </div>
            </div>
        </section>
    {/block}
{/if}

{block name='tecdoc_page_content'}
    <div class="tecdoc-assembly-groups">
        {if $show_vehicle_search}
            <h4 class="tecdoc-assembly-groups__heading tecdoc-heading">{l s='Subcategories' d='Modules.Itptecdoc.Shop'}</h4>
        {/if}

        {include file='module:itp_tecdoc/views/templates/front/components/assembly-groups.tpl' assembly_groups=$assembly_group->subgroups}
    </div>
{/block}