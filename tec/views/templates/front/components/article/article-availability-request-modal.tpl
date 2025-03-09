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

{block name='javascript_head'}
    {if $recaptcha_enable and $recaptcha_site_key}
        <script src="https://www.google.com/recaptcha/api.js?render={$recaptcha_site_key}"></script>

        <script>
            let recaptchaSiteKey = '{$recaptcha_site_key}';
        </script>
    {/if}
{/block}

<div
        class="tecdoc-modal tecdoc-modal--small"
        role="dialog"
        tabindex="-1"
        x-data="{ showModal: false }"
        x-show="showModal"
        x-trap.noscroll.inert="showModal"
        @show-availability-request-modal.window="showModal = !showModal;"
        @keydown.escape="showModal = false"
        x-cloak
>
    <div class="tecdoc-modal__dialog">
        <div class="tecdoc-modal__content" x-show="showModal" x-transition x-cloak>
            <div class="tecdoc-modal__header">
                <h3 class="tecdoc-modal__title">{l s='Availability request' d='Modules.Itptecdoc.Shop'}</h3>
                <a href="#" class="tecdoc-modal__close" @click.prevent="showModal = false"></a>
            </div>

            <div class="tecdoc-modal__body">
                <form
                        action="{$link->getModuleLink('itp_tecdoc','functions', [], true)}"
                        method="POST"
                        class="tecdoc-form"
                        x-data="tecdocAvailabilityRequestForm()"
                        x-ref="form"
                        @show-availability-request-modal.window="form.product = $event.detail.product;"
                        @submit.prevent="submit"
                >
                    <div class="tecdoc-form__text">
                        {l s='Fill out the form, and we\'ll get in touch with you as soon as possible.' d='Modules.Itptecdoc.Shop'}
                    </div>

                    <div class="tecdoc-form__text tecdoc-form__text--success" x-text="message" x-show="message" x-cloak></div>

                    <div class="tecdoc-form__fields">
                        <div class="tecdoc-form__field tecdoc-form__field--required">
                            <label for="tecdocArticle" class="tecdoc-form__label">{l s='Product' d='Modules.Itptecdoc.Shop'}</label>
                            <input type="text" class="tecdoc-form__input" id="tecdocArticle" placeholder="{l s='Enter product' d='Modules.Itptecdoc.Shop'}" :class="{ 'tecdoc-form__input--error': 'product' in errors }" x-model="form.product" disabled required>

                            <div class="tecdoc-form__error-message" x-text="errors['product']" x-show="'product' in errors" x-cloak></div>
                        </div>

                        <div class="tecdoc-form__field tecdoc-form__field--required">
                            <label for="tecdocQuantity" class="tecdoc-form__label">{l s='Quantity' d='Modules.Itptecdoc.Shop'}</label>
                            <input type="number" class="tecdoc-form__input" id="tecdocQuantity" placeholder="{l s='Enter quantity' d='Modules.Itptecdoc.Shop'}" :class="{ 'tecdoc-form__input--error': 'quantity' in errors }" x-model="form.qty" required>

                            <div class="tecdoc-form__error-message" x-text="errors['qty']" x-show="'qty' in errors" x-cloak></div>
                        </div>

                        <div class="tecdoc-form__field tecdoc-form__field--required">
                            <label for="tecdocEmail" class="tecdoc-form__label">{l s='E-mail' d='Modules.Itptecdoc.Shop'}</label>
                            <input type="email" class="tecdoc-form__input" id="tecdocEmail" placeholder="{l s='Enter e-mail' d='Modules.Itptecdoc.Shop'}" :class="{ 'tecdoc-form__input--error': 'email' in errors }" x-model="form.email" required>

                            <div class="tecdoc-form__error-message" x-text="errors['email']" x-show="'email' in errors" x-cloak></div>
                        </div>

                        <div class="tecdoc-form__field">
                            <label for="tecdocComment" class="tecdoc-form__label">{l s='Comment' d='Modules.Itptecdoc.Shop'}</label>
                            <textarea class="tecdoc-form__textarea" id="tecdocComment" placeholder="{l s='Enter comment' d='Modules.Itptecdoc.Shop'}" :class="{ 'tecdoc-form__input--error': 'comment' in errors }" x-model="form.comment"></textarea>

                            <div class="tecdoc-form__error-message" x-text="errors['comment']" x-show="'comment' in errors" x-cloak></div>
                        </div>
                    </div>

                    <button class="tecdoc-form__button tecdoc-button tecdoc-button--loading" :class="{ 'tecdoc-button--loading': loading }" :disabled="loading">
                        <span class="tecdoc-button__content">{l s='Send' d='Modules.Itptecdoc.Shop'}</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="tecdoc-modal__backdrop" x-show="showModal" @click="showModal = false" x-transition.opacity></div>
</div>