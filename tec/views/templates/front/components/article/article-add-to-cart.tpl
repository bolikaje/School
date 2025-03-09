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

{assign var='modalKey' value=md5(serialize([$article->brandName, $article->reference]))}

<form action="{$urls.pages.cart}" method="POST" class="tecdoc-article__form tecdoc-add-to-cart-form">
    <input type="hidden" name="token" value="{$static_token}">
    <input type="hidden" name="article_type" value="{$article->getType()->value}">

    {if $article->getType()->value == 0}
        <input type="hidden" name="id_tecdoc_brand" value="{$article->brandId}">
        <input type="hidden" name="article_reference" value="{$article->reference}">
    {else}
        <input type="hidden" name="id_custom_article" value="{$article->getId()}">
    {/if}

    <input type="hidden" name="id_tecdoc_supplier" value="{$availability->tecdocSupplierId}"/>

    {if $article->availability->count() > 1}
        <a href="#" class="tecdoc-add-to-cart-form__show-availability-options tecdoc-link tecdoc-link--underline" @click.prevent="$dispatch('show-availability-modal-{$modalKey}')">{l s='Price and delivery options' d='Modules.Itptecdoc.Shop'}</a>
    {/if}

    <div class="tecdoc-add-to-cart-form__actions">
        <div class="tecdoc-add-to-cart-form__quantity-input tecdoc-quantity" x-data="tecdocQuantityInput()">
            <button class="tecdoc-quantity__minus" @click.prevent="qty--"></button>

            <input
                    type="number"
                    class="tecdoc-quantity__input"
                    name="qty"
                    value="{$availability->minimumOrderQuantity}"
                    min="{$availability->minimumOrderQuantity}"
                    max="{$availability->stock}"
                    title="{l s='Quantity' d='Modules.Itptecdoc.Shop'}"
                    x-model="qty"
                    x-ref="input"
                    @change="validateInput($event.target.value)"
            >

            <button class="tecdoc-quantity__plus" @click.prevent="qty++"></button>
        </div>

        <button type="submit" class="tecdoc-add-to-cart-form__button tecdoc-button tecdoc-button--add-to-cart" data-button-action="add-to-cart">
            <span class="tecdoc-add-to-cart-form__text">
                {l s='Add to cart' d='Modules.Itptecdoc.Shop'}
            </span>
        </button>
    </div>

    {if $availability->minimumOrderQuantity > 1}
        <div class="tecdoc-add-to-cart-form__minimum-order-quantity">{l s='Minimum order quantity: %s pcs' sprintf=[$availability->minimumOrderQuantity] d='Modules.Itptecdoc.Shop'}</div>
    {/if}
</form>

{include file='module:itp_tecdoc/views/templates/front/components/article/article-availability-options.tpl'}