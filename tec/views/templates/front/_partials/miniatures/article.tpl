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

<div class="tecdoc-article tecdoc-article--miniature" :class="{ 'tecdoc-article--vertical' : view === 1 }" itemprop="itemListElement" itemscope itemtype="https://schema.org/Product">
    {include file='module:itp_tecdoc/views/templates/front/components/article/article-brand-information.tpl'}

    <div class="tecdoc-article__row">
        <div class="tecdoc-article__images">
            <a href="{$article->getLink()}" class="tecdoc-article__link tecdoc-link">
                <img class="tecdoc-article__image" src="{$article->getCoverImage()}" alt="{$article->getName()}" title="{$article->getName()}" itemprop="image">
            </a>
        </div>

        <div class="tecdoc-article__content">
            <h3 class="tecdoc-article__title" itemprop="name">
                <a href="{$article->getLink()}" class="tecdoc-article__link tecdoc-link">{$article->getName()}</a>
            </h3>

            <div class="tecdoc-article__metas">
                <div class="tecdoc-article__meta tecdoc-article__meta--brand">
                    <div class="tecdoc-article__meta-title">{l s='Brand' d='Modules.Itptecdoc.Shop'}:</div>
                    <div class="tecdoc-article__meta-value" itemprop="brand" itemscope itemtype="https://schema.org/Brand">{$article->brandName}</div>
                </div>

                <div class="tecdoc-article__meta tecdoc-article__meta--reference">
                    <div class="tecdoc-article__meta-title">{l s='Reference' d='Modules.Itptecdoc.Shop'}:</div>
                    <div class="tecdoc-article__meta-value">{$article->reference}</div>
                </div>
            </div>

            {if not $article->getGroupedCriteria()->isEmpty()}
                <div class="tecdoc-article__attributes">
                    {foreach from=$article->getGroupedCriteria()->slice(0, 5) item=$criteria}
                        <div class="tecdoc-article__attribute">
                            <div class="tecdoc-article__attribute-title">{$criteria->description}:</div>
                            <div class="tecdoc-article__attribute-value">{implode(', ', array_column($criteria->values->toArray(), 'formattedValue'))}</div>
                        </div>
                    {/foreach}
                </div>
            {/if}
        </div>

        {include file='module:itp_tecdoc/views/templates/front/components/article/article-availability.tpl'}
    </div>
</div>