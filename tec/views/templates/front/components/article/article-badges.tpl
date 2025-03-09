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

<div class="tecdoc-article__badges">
    {if not $article->availability->isEmpty()}
        {assign var='availability' value=$article->availability->first()}

        <div class="tecdoc-article__badge tecdoc-article__badge--in-stock">{l s='In stock' d='Modules.Itptecdoc.Shop'}</div>

{*        <div class="tecdoc-article__badge tecdoc-article__badge--in-stock">*}
{*            {l s='In stock' d='Modules.Itptecdoc.Shop'}*}

{*            {if $availability->stock == 1}*}
{*                {l s='1 pc' d='Modules.Itptecdoc.Shop'}*}
{*            {else}*}
{*                {l s='%s pcs' sprintf=[$availability->stock] d='Modules.Itptecdoc.Shop'}*}
{*            {/if}*}
{*        </div>*}

{*        <div class="tecdoc-article__badge tecdoc-article__badge--delivery">*}
{*            {if $availability->deliveryTime == 1}*}
{*                {l s='Up to 1 day' d='Modules.Itptecdoc.Shop'}*}
{*            {else}*}
{*                {l s='Up to %s days' sprintf=[$availability->deliveryTime] d='Modules.Itptecdoc.Shop'}*}
{*            {/if}*}
{*        </div>*}

        {if $availability->prices->displayedDiscountRate}
            <div class="tecdoc-article__badge tecdoc-article__badge--discount">{l s='Discount' d='Modules.Itptecdoc.Shop'}</div>
        {/if}
    {else}
        <div class="tecdoc-article__badge tecdoc-article__badge--out-of-stock">{l s='Out of stock' d='Modules.Itptecdoc.Shop'}</div>
    {/if}
</div>