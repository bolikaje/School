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

{function name="categories" nodes=[] depth=0}
    {strip}
        {if $nodes|count}
            {foreach from=$nodes item=$node}
                <div class="tecdoc-assembly-group tecdoc-assembly-group--small" x-data="{ expand: {var_export(in_array($node->id, $c_tree_path), true)} }">
                    <div class="tecdoc-assembly-group__row">
                        <a href="{$node->getLink($vehicle)}" class="tecdoc-assembly-group__link{if $assembly_group->id == $node->id} tecdoc-assembly-group__link--active{/if} tecdoc-link">
                            {$node->name}
                        </a>

                        {if $node->subgroups->count() > 0}
                            <span class="tecdoc-assembly-group__toggle" :class="{ 'tecdoc-assembly-group__toggle--expanded' : expand }" @click="expand = !expand"></span>
                        {/if}
                    </div>

                    {if $node->subgroups->count() > 0}
                        <div class="tecdoc-assembly-group__subgroups tecdoc-assembly-group__subgroups--level-{$depth}" x-show="expand" x-cloak>
                            {categories nodes=$node->subgroups depth=$depth+1}
                        </div>
                    {/if}
                </div>
            {/foreach}
        {/if}
    {/strip}
{/function}

{if isset($assembly_groups)}
    <div class="tecdoc-assembly-groups-menu">
        <div class="tecdoc-assembly-groups-menu__header">{l s='Categories' d='Modules.Itptecdoc.Shop'}</div>

        <div class="tecdoc-assembly-groups-menu__body tecdoc-scroll">
            {categories nodes=$assembly_groups}
        </div>
    </div>
{/if}