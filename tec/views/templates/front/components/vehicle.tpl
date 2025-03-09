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

{if isset($vehicle)}
    <div class="tecdoc-vehicle">
        <div class="tecdoc-vehicle__grid">
            <div class="tecdoc-vehicle__information">
                <div class="tecdoc-vehicle__title">{l s='Make' d='Modules.Itptecdoc.Shop'}:</div>
                <div class="tecdoc-vehicle__value">{$vehicle->manufacturerName}</div>
            </div>

            <div class="tecdoc-vehicle__information">
                <div class="tecdoc-vehicle__title">{l s='Model' d='Modules.Itptecdoc.Shop'}:</div>
                <div class="tecdoc-vehicle__value">{$vehicle->modelSeriesName}</div>
            </div>

            {if $vehicle->description}
                <div class="tecdoc-vehicle__information">
                    <div class="tecdoc-vehicle__title">{l s='Vehicle' d='Modules.Itptecdoc.Shop'}:</div>
                    <div class="tecdoc-vehicle__value">{$vehicle->description}</div>
                </div>
            {/if}

            {if $vehicle->cylinders or $vehicle->capacityCC}
                <div class="tecdoc-vehicle__information">
                    <div class="tecdoc-vehicle__title">{l s='Engine' d='Modules.Itptecdoc.Shop'}:</div>

                    <div class="tecdoc-vehicle__value">
                        {if $vehicle->cylinders}
                            <span class="tecdoc-vehicle__engine-cylinders">{l s='%s cylinders' sprintf=[$vehicle->cylinders] d='Modules.Itptecdoc.Shop'}</span>
                        {/if}

                        {if $vehicle->capacityCC}
                            <span class="tecdoc-vehicle__engine-capacity">
                                {l s='(%s sm³)' sprintf=[$vehicle->capacityCC] d='Modules.Itptecdoc.Shop'}
                            </span>
                        {/if}

{*                        <div class="tecdoc-vehicle__motor-codes">*}
{*                            {foreach from=$vehicle->engines item=$engine}*}
{*                                <div class="tecdoc-vehicle__motor-code">{$engine->code}</div>*}
{*                            {/foreach}*}
{*                        </div>*}
                    </div>
                </div>
            {/if}

            {if $vehicle->fuelType}
                <div class="tecdoc-vehicle__information">
                    <div class="tecdoc-vehicle__title">{l s='Fuel' d='Modules.Itptecdoc.Shop'}:</div>
                    <div class="tecdoc-vehicle__value">{$vehicle->fuelType}</div>
                </div>
            {/if}

            <div class="tecdoc-vehicle__information">
                <div class="tecdoc-vehicle__title">{l s='Years' d='Modules.Itptecdoc.Shop'}:</div>
                <div class="tecdoc-vehicle__value">{l s='%s - %s' sprintf=[$vehicle->getDateFromLabel(), $vehicle->getDateToLabel()] d='Modules.Itptecdoc.Shop'}</div>
            </div>

            <div class="tecdoc-vehicle__information">
                <div class="tecdoc-vehicle__title">{l s='Power' d='Modules.Itptecdoc.Shop'}:</div>
                <div class="tecdoc-vehicle__value">{l s='%s kw / %s hp' sprintf=[$vehicle->kiloWattsTo, $vehicle->horsePowerTo] d='Modules.Itptecdoc.Shop'}</div>
            </div>

            {if $vehicle->driveType}
                <div class="tecdoc-vehicle__information">
                    <div class="tecdoc-vehicle__title">{l s='Drive' d='Modules.Itptecdoc.Shop'}:</div>
                    <div class="tecdoc-vehicle__value">{$vehicle->driveType}</div>
                </div>
            {/if}
        </div>
    </div>
{/if}