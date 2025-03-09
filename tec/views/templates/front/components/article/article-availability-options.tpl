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

<div
        class="tecdoc-article__modal tecdoc-modal"
        role="dialog"
        tabindex="-1"
        x-data="{ showModal: false }"
        x-show="showModal"
        x-trap.noscroll.inert="showModal"
        @show-availability-modal-{$modalKey}.window="showModal = !showModal;"
        @keydown.escape="showModal = false"
        x-cloak
>
    <div class="tecdoc-modal__dialog">
        <div class="tecdoc-modal__content" x-show="showModal" x-transition x-cloak>
            <div class="tecdoc-modal__header">
                <h3 class="tecdoc-modal__title">{l s='Price and delivery options' d='Modules.Itptecdoc.Shop'}</h3>
                <a href="#" class="tecdoc-modal__close" @click.prevent="showModal = false"></a>
            </div>

            <div class="tecdoc-modal__body">
                <div class="tecdoc-availability-options">
                    <h6 class="tecdoc-availability-options__heading tecdoc-heading">{l s='Choose suitable option' d='Modules.Itptecdoc.Shop'}:</h6>

                    <div class="tecdoc-availability-options__list">
                        {foreach from=$article->availability item=$availability key=$key}
                            <form action="{$urls.pages.cart}" method="POST" class="tecdoc-availability-option tecdoc-availability-option--form">
                                <input type="hidden" name="token" value="{$static_token}">
                                <input type="hidden" name="article_type" value="{$article->getType()->value}">

                                {if $article->getType()->value == 0}
                                    <input type="hidden" name="id_tecdoc_brand" value="{$article->brandId}">
                                    <input type="hidden" name="article_reference" value="{$article->reference}">
                                {else}
                                    <input type="hidden" name="id_custom_article" value="{$article->getId()}">
                                {/if}

                                <input type="hidden" name="id_tecdoc_supplier" value="{$availability->tecdocSupplierId}"/>

                                <div class="tecdoc-availability-option__label">
                                    <span class="tecdoc-availability-option__prices-wrapper">
                                        <span class="tecdoc-availability-option__prices">
                                            {if $availability->prices->displayedDiscountRate}
                                                <span class="tecdoc-availability-option__old-price">
                                                    {$availability->prices->getDisplayedPriceWithoutReductionsFormatted()}
                                                </span>

                                                <span class="tecdoc-availability-option__price tecdoc-availability-option__price--with-discount">
                                                    {$availability->prices->getDisplayedPriceWithReductionsFormatted()}
                                                </span>
                                            {else}
                                                <span class="tecdoc-availability-option__price">{$availability->prices->getDisplayedPriceWithoutReductionsFormatted()}</span>
                                            {/if}
                                        </span>

                                        {if $availability->prices->displayedDeposit}
                                            <span class="tecdoc-availability-option__deposit">
                                                 {l s='+ deposit %s' sprintf=[$availability->prices->getDisplayedDepositFormatted()] d='Modules.Itptecdoc.Shop'}
                                            </span>
                                        {/if}
                                    </span>

                                    <span class="tecdoc-availability-option__quantity">
                                        {if $availability->stock == 1}
                                            {l s='1 pc' d='Modules.Itptecdoc.Shop'}
                                        {else}
                                            {l s='%s pcs' sprintf=[$availability->stock] d='Modules.Itptecdoc.Shop'}
                                        {/if}
                                    </span>

                                    <span class="tecdoc-availability-option__delivery-time">
                                        {if $availability->deliveryTime == 1}
                                            {l s='Up to 1 day' d='Modules.Itptecdoc.Shop'}
                                        {else}
                                            {l s='Up to %s days' sprintf=[$availability->deliveryTime] d='Modules.Itptecdoc.Shop'}
                                        {/if}

{*                                        {Tools::displayDate($availability->deliveryDate)}*}
                                    </span>
                                </div>

                                <div class="tecdoc-availability-option__actions">
                                    <div class="tecdoc-availability-option__quantity-input tecdoc-quantity" x-data="tecdocQuantityInput()">
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

                                    <button type="submit" class="tecdoc-availability-option__button tecdoc-button tecdoc-button--add-to-cart" data-button-action="add-to-cart"></button>
                                </div>
                            </form>
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="tecdoc-modal__backdrop" x-show="showModal" @click="showModal = false" x-transition.opacity></div>
</div>
