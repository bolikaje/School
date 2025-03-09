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

{$tabs = array_filter([
    'attributes' => !$article->criteria->isEmpty(),
    'oem' => !$article->oemNumbers->isEmpty(),
    'compatibles' => isset($linked_manufacturers) and !$linked_manufacturers->isEmpty(),
    'replacements' => !$article->replacements->isEmpty()
])}

{if not empty($tabs)}
    <div class="tecdoc-tabs" x-data="{ currentTab: '{key($tabs)}' }">
        <div class="tecdoc-tabs__buttons">
            {if not $article->criteria->isEmpty()}
                <a href="#" class="tecdoc-tabs__button tecdoc-link tecdoc-tabs__button--active" :class="{ 'tecdoc-tabs__button--active': currentTab === 'attributes' }" @click.prevent="currentTab = 'attributes'">{l s='Attributes' d='Modules.Itptecdoc.Shop'}</a>
            {/if}

            {if not $article->oemNumbers->isEmpty()}
                <a href="#" class="tecdoc-tabs__button tecdoc-link" :class="{ 'tecdoc-tabs__button--active': currentTab === 'oem' }" @click.prevent="currentTab = 'oem'">{l s='OEM numbers' d='Modules.Itptecdoc.Shop'}</a>
            {/if}

            {if isset($linked_manufacturers) and not $linked_manufacturers->isEmpty()}
                <a href="#" class="tecdoc-tabs__button tecdoc-link" :class="{ 'tecdoc-tabs__button--active': currentTab === 'compatibles' }" @click.prevent="currentTab = 'compatibles'">{l s='Compatibility' d='Modules.Itptecdoc.Shop'}</a>
            {/if}

            {if not $article->replacements->isEmpty()}
                <a href="#" class="tecdoc-tabs__button tecdoc-link" :class="{ 'tecdoc-tabs__button--active': currentTab === 'replacements' }" @click.prevent="currentTab = 'replacements'">{l s='Similar articles' d='Modules.Itptecdoc.Shop'}</a>
            {/if}
        </div>

        <div class="tecdoc-tabs__panels">
            {if not $article->getGroupedCriteria()->isEmpty()}
                {math equation="ceil(attributesCount / 2)" attributesCount=$article->getGroupedCriteria()|@count assign=attributesColumnLength}

                <div class="tecdoc-tabs__panel" x-show="currentTab === 'attributes'">
                    <div class="tecdoc-tabs__row">
                        {foreach from=array_chunk($article->getGroupedCriteria()->toArray(), $attributesColumnLength, true) item=$attributues}
                            <div class="tecdoc-tabs__column">
                                {foreach from=$attributues item=$criteria}
                                    <div class="tecdoc-data-row">
                                        <div class="tecdoc-data-row__title">{$criteria->description}:</div>
                                        <div class="tecdoc-data-row__value">{implode(', ', array_column($criteria->values->toArray(), 'formattedValue'))}</div>
                                    </div>
                                {/foreach}
                            </div>
                        {/foreach}
                    </div>
                </div>
            {/if}

            {if not $article->oemNumbers->isEmpty()}
                {math equation="ceil(attributesCount / 3)" attributesCount=$article->oemNumbers|@count assign=oemNumbersColumnLength}

                <div class="tecdoc-tabs__panel" x-show="currentTab === 'oem'" x-cloak>
                    <div class="tecdoc-tabs__row">
                        {foreach from=array_chunk($article->oemNumbers->toArray(), $oemNumbersColumnLength, true) item=$oemNumbers}
                            <div class="tecdoc-tabs__column">
                                {foreach from=$oemNumbers item=$oemNumber}
                                    <div class="tecdoc-data-row">
                                        <div class="tecdoc-data-row__title">{$oemNumber->manufacturerName}</div>

                                        <div class="tecdoc-data-row__value">
                                            <a href="{$link->getModuleLink('itp_tecdoc', 'search', ['search_query' => $oemNumber->articleNumber], true)}" class="tecdoc-data-row__link tecdoc-link tecdoc-link--underline">{$oemNumber->articleNumber}</a>
                                        </div>
                                    </div>
                                {/foreach}
                            </div>
                        {/foreach}
                    </div>
                </div>
            {/if}

            {if isset($linked_manufacturers) and not $linked_manufacturers->isEmpty()}
                <div class="tecdoc-tabs__panel" x-show="currentTab === 'compatibles'" x-cloak>
                    {include file='module:itp_tecdoc/views/templates/front/components/article/article-compatibles.tpl'}
                </div>
            {/if}

            {if not $article->replacements->isEmpty()}
                {math equation="ceil(attributesCount / 3)" attributesCount=$article->replacements|@count assign=similarArticlesColumnLength}

                <div class="tecdoc-tabs__panel" x-show="currentTab === 'replacements'">
                    <div class="tecdoc-tabs__row">
                        {foreach from=array_chunk($article->replacements->toArray(), $similarArticlesColumnLength, true) item=$replacements}
                            <div class="tecdoc-tabs__column">
                                {foreach from=$replacements item=$replacement}
                                    <div class="tecdoc-data-row">
                                        <div class="tecdoc-data-row__title">{$replacement->brandName}</div>
                                        <div class="tecdoc-data-row__value">{$replacement->articleNumber}</div>
                                    </div>
                                {/foreach}
                            </div>
                        {/foreach}
                    </div>
                </div>
            {/if}
        </div>
    </div>
{/if}