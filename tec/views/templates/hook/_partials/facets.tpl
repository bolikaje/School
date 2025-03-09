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

{if $show_facets}
    {assign var='expanded' value=count($facets) <= 3}

    <form class="tecdoc-facets" x-data="tecdocFacets()" x-ref="form">
        <div class="tecdoc-facets__header">{l s='Filters' d='Modules.Itptecdoc.Shop'}</div>

        <div class="tecdoc-facets__body">
            <input type="hidden" name="sort_order" value="{$sort_order->value}"/>

            {if isset($search_query) and $search_query}
                <input type="hidden" name="search_query" value="{$search_query}">
            {/if}

            <template x-for="(facet, facetIndex) in facets">
                <div class="tecdoc-facet" x-data="tecdocFacet(facets[facetIndex]['filters'], '{$expanded}')" :class="{ 'tecdoc-facet--active': expanded }">
                    <div class="tecdoc-facet__header" x-text="facet.label" @click="expanded = !expanded"></div>

                    <div class="tecdoc-facet__body" x-show="expanded">
                        <div class="tecdoc-facet__search-field">
                            <input type="text" class="tecdoc-facet__search" title="{l s='Search...' d='Modules.Itptecdoc.Shop'}" placeholder="{l s='Search...' d='Modules.Itptecdoc.Shop'}" x-model="search">
                        </div>

                        <div class="tecdoc-facet__not-found" x-show="!facetOptions.length" x-cloak>{l s='Not found' d='Modules.Itptecdoc.Shop'}</div>

                        <div class="tecdoc-facet__options tecdoc-scroll">
                            <template x-for="(facetOption, facetOptionIndex) in facetOptions">
                                <div class="tecdoc-facet__option">
                                    {literal}
                                        <input
                                                type="checkbox"
                                                class="tecdoc-facet__input"
                                                :name="`filters[${facet.inputName}][]`"
                                                :id="`tecdoc-facet-${facetIndex}-${facetOptionIndex}`"
                                                :value="facetOption.value"
                                                :checked="facetOption.active"
                                                @change="$refs.form.submit()"
                                        >

                                        <label class="tecdoc-facet__label" :for="`tecdoc-facet-${facetIndex}-${facetOptionIndex}`">
                                            <span class="tecdoc-facet__checkbox"></span>
                                            <span class="tecdoc-facet__content" x-text="facetOption.label"></span>
                                        </label>
                                    {/literal}

                                    {if $show_facets_count}
                                        <span class="tecdoc-facet__count" x-text="facetOption.count"></span>
                                    {/if}
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="tecdoc-facets__footer">
            <button type="button" class="tecdoc-button tecdoc-button--reset" @click.prevent="reset()" :disabled="!facets.some(facet => facet.filters.some(filter => filter.active))">{l s='Reset filters' d='Modules.Itptecdoc.Shop'}</button>
        </div>
    </form>
{/if}