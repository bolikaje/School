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
    {l s='Choose vehicle' d='Modules.Itptecdoc.Shop'}
{/block}

{block name='tecdoc_page_subtitle'}
    {l s='Choose suitable vehicle for %s %s' sprintf=[$manufacturer->name, $model_series->name] d='Modules.Itptecdoc.Shop'}
{/block}

{if $show_manufacturers_logo and $manufacturer->getImage(false)}
    {block name='tecdoc_page_title_after'}
        <img class="tecdoc-page-header__manufacturer-image" src="{$manufacturer->getImage(false)}" alt="{$manufacturer->name}"/>
    {/block}
{/if}

{block name='tecdoc_page_content'}
    {if not $vehicles->isEmpty()}
        <div class="tecdoc-vehicles">
            <div class="tecdoc-vehicles__list">
                <div class="tecdoc-vehicles__item tecdoc-vehicles__item--header">
                    <div class="tecdoc-vehicles__field tecdoc-vehicles__field--vehicle">{l s='Vehicle' d='Modules.Itptecdoc.Shop'}</div>
                    <div class="tecdoc-vehicles__field tecdoc-vehicles__field--engine">{l s='Engine' d='Modules.Itptecdoc.Shop'}</div>
                    <div class="tecdoc-vehicles__field tecdoc-vehicles__field--fuel">{l s='Fuel' d='Modules.Itptecdoc.Shop'}</div>
                    <div class="tecdoc-vehicles__field tecdoc-vehicles__field--year">{l s='Years' d='Modules.Itptecdoc.Shop'}</div>
                    <div class="tecdoc-vehicles__field tecdoc-vehicles__field--power">{l s='Power' d='Modules.Itptecdoc.Shop'}</div>
                    <div class="tecdoc-vehicles__field tecdoc-vehicles__field--drive">{l s='Drive' d='Modules.Itptecdoc.Shop'}</div>
                    <div class="tecdoc-vehicles__field tecdoc-vehicles__field--link"></div>
                </div>

                {foreach from=$vehicles item=$vehicle}
                    <div class="tecdoc-vehicles__item">
                        <div class="tecdoc-vehicles__field tecdoc-vehicles__field--vehicle">
                            <div class="tecdoc-vehicles__title">{l s='Vehicle' d='Modules.Itptecdoc.Shop'}</div>
                            <div class="tecdoc-vehicles__value">{$vehicle->description}</div>
                        </div>

                        <div class="tecdoc-vehicles__field tecdoc-vehicles__field--engine">
                            <div class="tecdoc-vehicles__title">{l s='Engine' d='Modules.Itptecdoc.Shop'}</div>

                            <div class="tecdoc-vehicles__value">
                                {if $vehicle->cylinders}
                                    <span class="tecdoc-vehicles__engine-cylinders">{l s='%s cylinders' sprintf=[$vehicle->cylinders] d='Modules.Itptecdoc.Shop'}</span>
                                {/if}

                                {if $vehicle->capacityLiters or $vehicle->capacityCC}
                                    <span class="tecdoc-vehicles__engine-capacity">
                                        {if $vehicle->capacityLiters}
                                            {l s='%s l' sprintf=[$vehicle->capacityLiters] d='Modules.Itptecdoc.Shop'}
                                        {/if}

                                        {if $vehicle->capacityCC}
                                            {l s='(%s sm³)' sprintf=[$vehicle->capacityCC] d='Modules.Itptecdoc.Shop'}
                                        {/if}
                                    </span>
                                {/if}

								{if not $vehicle->engines->isEmpty()}
									<div class="tecdoc-vehicles__motor-codes">
										{foreach from=$vehicle->engines item=$engine}
											<div class="tecdoc-vehicles__motor-code">{$engine->code}</div>
										{/foreach}
									</div>
								{/if}
                            </div>
                        </div>

                        <div class="tecdoc-vehicles__field tecdoc-vehicles__field--fuel">
                            <div class="tecdoc-vehicles__title">{l s='Fuel' d='Modules.Itptecdoc.Shop'}</div>
                            <div class="tecdoc-vehicles__value">{$vehicle->fuelType}</div>
                        </div>

                        <div class="tecdoc-vehicles__field tecdoc-vehicles__field--year">
                            <div class="tecdoc-vehicles__title">{l s='Years' d='Modules.Itptecdoc.Shop'}</div>

                            <div class="tecdoc-vehicles__value">
								{l s='%s - %s' sprintf=[$vehicle->getDateFromLabel(), $vehicle->getDateToLabel()] d='Modules.Itptecdoc.Shop'}
							</div>
                        </div>

                        <div class="tecdoc-vehicles__field tecdoc-vehicles__field--power">
                            <div class="tecdoc-vehicles__title">{l s='Power' d='Modules.Itptecdoc.Shop'}</div>

                            <div class="tecdoc-vehicles__value">
								{l s='%s kw / %s hp' sprintf=[$vehicle->kiloWattsTo, $vehicle->horsePowerTo] d='Modules.Itptecdoc.Shop'}
							</div>
                        </div>

                        <div class="tecdoc-vehicles__field tecdoc-vehicles__field--drive">
                            <div class="tecdoc-vehicles__title">{l s='Drive' d='Modules.Itptecdoc.Shop'}</div>

                            <div class="tecdoc-vehicles__value">
								{$vehicle->driveType}
							</div>
                        </div>

                        <div class="tecdoc-vehicles__field tecdoc-vehicles__field--link">
                            <a href="{$vehicle->getLink()}" class="tecdoc-vehicles__link tecdoc-link">{l s='Choose' d='Modules.Itptecdoc.Shop'}</a>
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    {else}
        {l s='No vehicles found' d='Modules.Itptecdoc.Shop'}
    {/if}
{/block}