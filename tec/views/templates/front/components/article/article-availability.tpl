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

<div class="tecdoc-article__availability">
    {include file='module:itp_tecdoc/views/templates/front/components/article/article-badges.tpl'}

    {if not $configuration.is_catalog and not $article->availability->isEmpty()}
        {assign var='availability' value=$article->availability->first()}

        <div class="tecdoc-article__prices-wrapper">
            <div class="tecdoc-article__prices">
                {if $availability->prices->displayedDiscountRate}
                    <div class="tecdoc-article__price tecdoc-article__price--discount" itemprop="price" content="{$availability->prices->displayedPriceWithReductions}">
                        {$availability->prices->getDisplayedPriceWithReductionsFormatted()}
                    </div>

                    <div class="tecdoc-article__old-price">{$availability->prices->getDisplayedPriceWithoutReductionsFormatted()}</div>
                {else}
                    <span class="tecdoc-article__price" itemprop="price">{$availability->prices->getDisplayedPriceWithoutReductionsFormatted()}</span>
                {/if}
            </div>

            {if $availability->prices->displayedDeposit}
                <div class="tecdoc-article__deposit">{l s='+ deposit %s' sprintf=[$availability->prices->getDisplayedDepositFormatted()] d='Modules.Itptecdoc.Shop'}</div>
            {/if}

            <div class="tecdoc-article__delivery-time">
                {if $availability->deliveryTime == 1}
                    {l s='Delivery up to 1 working day' d='Modules.Itptecdoc.Shop'}
                {else}
                    {l s='Delivery up to %s working days' sprintf=[$availability->deliveryTime] d='Modules.Itptecdoc.Shop'}
                {/if}

{*                {l s='Expected to be delivered at %s' sprintf=[Tools::displayDate($availability->deliveryDate)] d='Modules.Itptecdoc.Shop'}*}
            </div>
        </div>

        {block name='tecdoc_article_add_to_cart'}
            {include file='module:itp_tecdoc/views/templates/front/components/article/article-add-to-cart.tpl'}
        {/block}

    {elseif $allow_availability_requests}
        <button class="tecdoc-button tecdoc-button--availability-request" @click.prevent="$dispatch('show-availability-request-modal', { product: '{implode(' ', [$article->brandName, $article->reference])}' })">
            {l s='Availability request' d='Modules.Itptecdoc.Shop'}
        </button>
    {/if}
</div>