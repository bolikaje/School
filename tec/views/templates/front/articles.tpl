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
    {$page.title}
{/block}

{block name='tecdoc_page_subtitle'}
    {l s='Find the spare parts you need quickly and easily' d='Modules.Itptecdoc.Shop'}
{/block}

{if isset($manufacturer) and $show_manufacturers_logo and $manufacturer->getImage(false)}
    {block name='tecdoc_page_title_after'}
        <img class="tecdoc-page-header__manufacturer-image" src="{$manufacturer->getImage(false)}" alt="{$manufacturer->name}"/>
    {/block}
{/if}

{block name='tecdoc_page_content'}
    {if not $articles->isEmpty()}
        <div class="tecdoc-articles" x-data="tecdocArticles()">
            {include file='module:itp_tecdoc/views/templates/front/components/vehicle.tpl'}

            <div class="tecdoc-articles__header">
                <div class="tecdoc-articles__summary">
                    {l s='Showing %from%-%to% of %total% item(s)' d='Shop.Theme.Catalog' sprintf=['%from%' => $pagination.items_shown_from ,'%to%' => $pagination.items_shown_to, '%total%' => $pagination.total_items]}
                </div>

                <div class="tecdoc-articles__actions">
                    <div class="tecdoc-articles__view">
                        <a href="#" class="tecdoc-articles__view-link tecdoc-articles__view-link--list tecdoc-articles__view-link--active" :class="{ 'tecdoc-articles__view-link--active': view === 0 }" @click.prevent="view = 0"></a>
                        <a href="#" class="tecdoc-articles__view-link tecdoc-articles__view-link--grid" :class="{ 'tecdoc-articles__view-link--active': view === 1 }" @click.prevent="view = 1"></a>
                    </div>

                    <div class="tecdoc-articles__sort tecdoc-articles__dropdown tecdoc-dropdown" x-data="tecdocDropdown('{$sort_order->value}')" x-bind="trigger">
                        <div class="tecdoc-dropdown__preview" x-ref="preview" x-text="dropdownPreview">{$sort_order->label()}</div>

                        <div class="tecdoc-dropdown__body" x-show="expanded" x-anchor.bottom-end.offset.10="$refs.preview" x-cloak x-transition>
                            {foreach from=$sort_order->labels() key=$sort_order_key item=$sort_order}
                                <div class="tecdoc-dropdown__option" @click="selectOption('{$sort_order_key}'); sortArticles('{$sort_order_key}')">{$sort_order}</div>
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>

            <div class="tecdoc-articles__body">
                <div class="tecdoc-articles__list" :class="{ 'tecdoc-articles__list' : view === 0, 'tecdoc-articles__grid' : view === 1 }" itemscope itemtype="https://schema.org/ItemList">
                    {foreach from=$articles item=$article}
                        {include file='module:itp_tecdoc/views/templates/front/_partials/miniatures/article.tpl'}
                    {/foreach}
                </div>
            </div>

            <div class="tecdoc-articles__footer">
                {include file='module:itp_tecdoc/views/templates/front/components/pagination.tpl'}
            </div>
        </div>
        {include file='module:itp_tecdoc/views/templates/front/components/article/article-availability-request-modal.tpl'}
    {else}
        {l s='No articles found' d='Modules.Itptecdoc.Shop'}
    {/if}
{/block}