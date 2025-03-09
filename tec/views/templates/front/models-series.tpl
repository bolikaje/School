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
    {l s='Choose model' d='Modules.Itptecdoc.Shop'}
{/block}

{block name='tecdoc_page_subtitle'}
    {l s='Choose suitable model for %s' sprintf=[$manufacturer->name] d='Modules.Itptecdoc.Shop'}
{/block}

{if $show_manufacturers_logo and $manufacturer->getImage(false)}
    {block name='tecdoc_page_title_after'}
        <img class="tecdoc-page-header__manufacturer-image" src="{$manufacturer->getImage(false)}" alt="{$manufacturer->name}"/>
    {/block}
{/if}

{block name='tecdoc_page_content'}
    {if not $model_series->isEmpty()}
        <div class="tecdoc-models">
            {if $is_grouped}
                <div class="tecdoc-models__grid tecdoc-grid tecdoc-grid--dropdownable">
                    {foreach from=$model_series item=$series}
                        <div class="tecdoc-grid__item">
                            <div class="tecdoc-model">
                                <img class="tecdoc-model__image" src="{$series->getImage()}" alt="{$series->name}" title="{$series->name}">
                                <div class="tecdoc-model__title">{$series->name}</div>
                                <div class="tecdoc-model__toggle tecdoc-grid__toggle">{l s='Select model'  d='Modules.Itptecdoc.Shop'}</div>
                            </div>
                        </div>

                        <div class="tecdoc-grid__dropdown">
                            <div class="tecdoc-grouped-models">
                                {foreach from=$series->modelSeries item=$model}
                                    <div class="tecdoc-grouped-model">
                                        <div class="tecdoc-grouped-model__title">
                                            <a href="{$model->getLink()}" class="tecdoc-grouped-model__link tecdoc-link tecdoc-link--underline" title="{$model->name}">{$model->name}</a>
                                        </div>

                                        {if $model->yearFrom or $model->yearTo}
                                            <div class="tecdoc-model__years">{l s='%s - %s' sprintf=[$model->getDateFromLabel(), $model->getDateToLabel()] d='Modules.Itptecdoc.Shop'}</div>
                                        {/if}
                                    </div>
                                {/foreach}
                            </div>
                        </div>
                    {/foreach}
                </div>
            {else}
                <div class="tecdoc-models__grid tecdoc-grid">
                    {foreach from=$model_series item=$model}
                        <div class="tecdoc-grid__item">
                            <a href="{$model->getLink()}" class="tecdoc-model tecdoc-link" title="{$model->name}">
                                <img class="tecdoc-model__image" src="{$model->getImage()}" alt="{$model->name}" title="{$model->name}">
                                <div class="tecdoc-model__title">{$model->name}</div>

                                {if $model->yearFrom or $model->yearTo}
                                    <div class="tecdoc-model__years">{l s='%s - %s' sprintf=[$model->getDateFromLabel(), $model->getDateToLabel()] d='Modules.Itptecdoc.Shop'}</div>
                                {/if}
                            </a>
                        </div>
                    {/foreach}
                </div>
            {/if}
        </div>
    {else}
        {l s='No models found' d='Modules.Itptecdoc.Shop'}
    {/if}
{/block}