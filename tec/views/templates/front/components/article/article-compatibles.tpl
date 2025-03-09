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

<div class="tecdoc-compatibles" x-data="tecdocCompatibles()">
    {foreach from=$linked_manufacturers item=$manufacturer}
        <div class="tecdoc-compatible">
            <div class="tecdoc-compatible__manufacturer" @click="manufacturerId !== {$manufacturer->id} ? getModelSeries({$manufacturer->id}) : manufacturerId = null">
                <div class="tecdoc-compatible__toggle" :class="{ 'tecdoc-compatible__toggle--active': manufacturerId === {$manufacturer->id} }"></div>

                {if $show_manufacturers_logo and $manufacturer->getImage(false)}
                    <img class="tecdoc-compatible__image" src="{$manufacturer->getImage(false)}" alt="{$manufacturer->name}" title="{$manufacturer->name}">
                {/if}

                <div class="tecdoc-compatible__value">{$manufacturer->name}</div>
            </div>

            <div class="tecdoc-compatible__models" x-show="manufacturerId === {$manufacturer->id}" x-cloak>
                <div class="tecdoc-compatible__loader tecdoc-loader" x-show="loading">
                    {l s='Loading' d='Modules.Itptecdoc.Shop'}
                </div>

                <template x-for="(groupedVehicles, index) in groupedVehicles[{$manufacturer->id}]">
                    <div class="tecdoc-compatible__model">
                        <div class="tecdoc-compatible__toggle tecdoc-compatible__toggle--light" :class="{ 'tecdoc-compatible__toggle--active': selectedModel === index }" x-text="index" @click="selectedModel !== index ? selectedModel = index : selectedModel = null"></div>

                        <div class="tecdoc-compatible__vehicles tecdoc-vehicles" x-show="selectedModel === index" x-cloak>
                            <div class="tecdoc-vehicles__list">
                                <div class="tecdoc-vehicles__item tecdoc-vehicles__item--header">
                                    <div class="tecdoc-vehicles__field tecdoc-vehicles__field--year">{l s='Years' d='Modules.Itptecdoc.Shop'}</div>
                                    <div class="tecdoc-vehicles__field tecdoc-vehicles__field--engine">{l s='Engine' d='Modules.Itptecdoc.Shop'}</div>
                                    <div class="tecdoc-vehicles__field tecdoc-vehicles__field--power">{l s='Power' d='Modules.Itptecdoc.Shop'}</div>
                                    <div class="tecdoc-vehicles__field tecdoc-vehicles__field--fuel">{l s='Fuel' d='Modules.Itptecdoc.Shop'}</div>
                                    <div class="tecdoc-vehicles__field tecdoc-vehicles__field--vehicle">{l s='Body' d='Modules.Itptecdoc.Shop'}</div>
                                </div>

                                <template x-for="vehicle in groupedVehicles">
                                    <div class="tecdoc-vehicles__item">
                                        <div class="tecdoc-vehicles__field tecdoc-vehicles__field--year">
                                            <div class="tecdoc-vehicles__title">{l s='Years' d='Modules.Itptecdoc.Shop'}</div>
                                            <div class="tecdoc-vehicles__value" x-text="vehicle.details.yearsLabel"></div>
                                        </div>

                                        <div class="tecdoc-vehicles__field tecdoc-vehicles__field--engine">
                                            <div class="tecdoc-vehicles__title">{l s='Engine' d='Modules.Itptecdoc.Shop'}</div>

                                            <div class="tecdoc-vehicles__value">
                                                <template x-if="vehicle.details.cylinder">
                                                    <span class="tecdoc-vehicles__engine-cylinders" x-text="sprintf(cylindersTranslation, vehicle.details.cylinder)"></span>
                                                </template>

                                                <template x-if="vehicle.details.cylinderCapacityLiter || vehicle.details.cylinderCapacityCcm">
                                                    <span class="tecdoc-vehicles__engine-capacity">
                                                        <template x-if="vehicle.details.cylinderCapacityLiter">
                                                            <span class="tecdoc-vehicles__engine-capacity-liter" x-text="sprintf(capacityLiterTranslation, vehicle.details.cylinderCapacityLiter)"></span>
                                                        </template>

                                                        <template x-if="vehicle.details.cylinderCapacityCcm">
                                                            <span class="tecdoc-vehicles__engine-capacity-ccm" x-text="sprintf(capacityCcmTranslation, vehicle.details.cylinderCapacityCcm)"></span>
                                                        </template>
                                                    </span>
                                                </template>

                                                <template x-for="motorCode in vehicle.motorCodes">
                                                    <div class="tecdoc-vehicles__motor-code" x-text="motorCode.motorCode"></div>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="tecdoc-vehicles__field tecdoc-vehicles__field--power">
                                            <div class="tecdoc-vehicles__title">{l s='Power' d='Modules.Itptecdoc.Shop'}</div>
                                            <div class="tecdoc-vehicles__value" x-text="sprintf(powerTranslation, vehicle.details.powerKw, vehicle.details.powerHp)"></div>
                                        </div>

                                        <div class="tecdoc-vehicles__field tecdoc-vehicles__field--fuel">
                                            <div class="tecdoc-vehicles__title">{l s='Fuel' d='Modules.Itptecdoc.Shop'}</div>
                                            <div class="tecdoc-vehicles__value" x-text="vehicle.details.fuelType"></div>
                                        </div>

                                        <div class="tecdoc-vehicles__field tecdoc-vehicles__field--vehicle">
                                            <div class="tecdoc-vehicles__title">{l s='Body' d='Modules.Itptecdoc.Shop'}</div>
                                            <div class="tecdoc-vehicles__value" x-text="vehicle.details.constructionType"></div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    {/foreach}
</div>