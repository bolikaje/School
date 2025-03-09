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

{if not $assembly_groups->isEmpty()}
    <div class="tecdoc-assembly-groups__grid tecdoc-grid tecdoc-grid--dropdownable">
        {foreach from=$assembly_groups item=$assembly_group}
            {if $assembly_group->subgroups->isEmpty()}
                <div class="tecdoc-grid__item tecdoc-grid__item--has-link">
                    <a href="{$assembly_group->getLink($vehicle)}" class="tecdoc-assembly-group tecdoc-link" title="{$assembly_group->name}">
                        <img class="tecdoc-assembly-group__image" src="{$assembly_group->getImage()}" alt="{$assembly_group->name}" title="{$assembly_group->name}">
                        <div class="tecdoc-assembly-group__title">{$assembly_group->name}</div>
                    </a>
                </div>
            {else}
                <div class="tecdoc-grid__item">
                    <div class="tecdoc-assembly-group">
                        <img class="tecdoc-assembly-group__image" src="{$assembly_group->getImage()}" alt="{$assembly_group->name}" title="{$assembly_group->name}">
                        <div class="tecdoc-assembly-group__title">{$assembly_group->name}</div>
                    </div>
                </div>

                <div class="tecdoc-grid__dropdown">
                    <div class="tecdoc-assembly-subgroups">
                        {if not $assembly_group->sortedSubgroups->isEmpty()}
                            {foreach from=$assembly_group->sortedSubgroups item=$sorted_subgroup}
                                <div class="tecdoc-assembly-subgroups__wrapper">
                                    <div class="tecdoc-assembly-subgroups__heading">{$sorted_subgroup->name}</div>

                                    <div class="tecdoc-assembly-subgroups__grid">
                                        {foreach from=$sorted_subgroup->subgroups item=$subgroup}
                                            <div class="tecdoc-assembly-subgroup">
                                                <a href="{$subgroup->getLink($vehicle)}" class="tecdoc-assembly-subgroup__title tecdoc-link tecdoc-link--underline" title="{$subgroup->name}">{$subgroup->name}</a>
                                            </div>
                                        {/foreach}
                                    </div>
                                </div>
                            {/foreach}
                        {/if}

                        {if not $assembly_group->unsortedSubgroups->isEmpty()}
                            <div class="tecdoc-assembly-subgroups__wrapper">
                                {if not $assembly_group->sortedSubgroups->isEmpty()}
                                    <div class="tecdoc-assembly-subgroups__heading">{l s='Other' d='Modules.Itptecdoc.Shop'}</div>
                                {/if}

                                <div class="tecdoc-assembly-subgroups__grid">
                                    {foreach from=$assembly_group->unsortedSubgroups item=$unsorted_subgroup}
                                        <div class="tecdoc-assembly-subgroup">
                                            <a href="{$unsorted_subgroup->getLink($vehicle)}" class="tecdoc-assembly-subgroup__title tecdoc-link tecdoc-link--underline" title="{$unsorted_subgroup->name}">{$unsorted_subgroup->name}</a>
                                        </div>
                                    {/foreach}
                                </div>
                            </div>
                        {/if}
                    </div>
                </div>
            {/if}
        {/foreach}
    </div>
{else}
    {l s='No groups found' d='Modules.Itptecdoc.Shop'}
{/if}