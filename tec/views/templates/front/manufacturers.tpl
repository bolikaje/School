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
    {l s='Choose manufacturer' d='Modules.Itptecdoc.Shop'}
{/block}

{block name='tecdoc_page_subtitle'}
    {l s='Begin exploring our catalog by selecting a suitable manufacturer' d='Modules.Itptecdoc.Shop'}
{/block}

{block name='tecdoc_page_content'}
    <div class="tecdoc-manufacturers" x-data="tecdocManufacturers(manufacturers)">
        {if $accessible_linking_target_types and count($accessible_linking_target_types) > 1}
            <div class="tecdoc-manufacturers__linkage-target-types tecdoc-linkage-target-types">
                {foreach from=$accessible_linking_target_types item=$accessible_linking_target}
                    <a href="{$link->getModuleLink('itp_tecdoc', 'manufacturers', ['linking_target_type_slug' => $accessible_linking_target->slug()])}" class="tecdoc-linkage-target-type tecdoc-linkage-target-type--{$accessible_linking_target->css()}{if $accessible_linking_target == $linking_target_type} tecdoc-linkage-target-type--active{/if} tecdoc-link">
                        <div class="tecdoc-linkage-target-type__title">{$accessible_linking_target->label()}</div>
                    </a>
                {/foreach}
            </div>
        {/if}

        {if $show_alphabetical_filter}
            <div class="tecdoc-manufacturers__alphabetical-filter tecdoc-alphabetical-filter">
                <a href="#" class="tecdoc-alphabetical-filter__item tecdoc-alphabetical-filter__item--active tecdoc-link" :class="{ 'tecdoc-alphabetical-filter__item--active' : alphabeticalFilter === null }" @click="alphabeticalFilter = null">All</a>

                {foreach from=$alphabetical_filters item=$letter}
                    <a href="#" class="tecdoc-alphabetical-filter__item tecdoc-link" :class="{ 'tecdoc-alphabetical-filter__item--active' : alphabeticalFilter === '{$letter}' }" @click.prevent="alphabeticalFilter = '{$letter}'">{$letter}</a>
                {/foreach}
            </div>
        {/if}

        <div class="tecdoc-manufacturers__grid tecdoc-grid">
            {if not $manufacturers->isEmpty()}
                <template x-for="(manufacturer, index) in manufacturers">
                    <div class="tecdoc-grid__item">
                        <a class="tecdoc-manufacturer tecdoc-link" :href="manufacturer.link" :title="manufacturer.name">
                            {if $show_manufacturers_logo}
                                <img class="tecdoc-manufacturer__image" :src="manufacturer.image" :alt="manufacturer.name" :title="manufacturer.name">
                            {/if}

                            <div class="tecdoc-manufacturer__title" x-text="manufacturer.name"></div>
                            <div class="tecdoc-manufacturer__chevron"></div>
                        </a>
                    </div>
                </template>
            {else}
                {l s='No manufacturers found' d='Modules.Itptecdoc.Shop'}
            {/if}
        </div>
    </div>
{/block}