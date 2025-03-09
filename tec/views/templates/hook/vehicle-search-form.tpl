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

{if not $accessible_linking_target_types->isEmpty()}
    <div
            class="tecdoc-vehicle-search__option tecdoc-vehicle-search__option--by-steps"
            x-data="tecdocVehicleSearch('{$linking_target_type->value}')"
            x-show="selectedSearchType === {$position}"
            {if $position > 1}x-cloak{/if}
    >
        <form
                action="{$link->getModuleLink('itp_tecdoc', 'vehicle-search', [], true)}"
                class="tecdoc-vehicle-search__form"
                x-ref="form"
        >
            {if $show_linkage_target_types and count($accessible_linking_target_types) > 1}
                <div class="tecdoc-vehicle-search__linkage-target-types tecdoc-linkage-target-types">
                    {foreach from=$accessible_linking_target_types item=$accessible_linking_target}
                        <a
                                href="#"
                                class="tecdoc-linkage-target-type tecdoc-linkage-target-type--{$accessible_linking_target->css()} tecdoc-link"
                                title="{$accessible_linking_target->label()}"
                                :class="{ 'tecdoc-linkage-target-type--active': linkingTargetType === '{$accessible_linking_target->value}' }"
                                @click.prevent="linkingTargetType = '{$accessible_linking_target->value}'"
                        >
                            <span class="tecdoc-linkage-target-type__name">{$accessible_linking_target->label()}</span>
                        </a>
                    {/foreach}
                </div>
            {/if}

            <input type="hidden" name="linking_target_type" x-model="linkingTargetType">

            {if isset($assembly_group)}
                <input type="hidden" name="assembly_group_id" value="{$assembly_group->id}">
            {/if}

            <div class="tecdoc-vehicle-search__fields">
                <div class="tecdoc-vehicle-search__selector tecdoc-selector tecdoc-selector--selected" :class="{ 'tecdoc-selector--selected': manufacturerId, 'tecdoc-selector--active': dropdownIndex === 1 }">
                    <div class="tecdoc-selector__field" x-ref="manufacturers" @click="toggleDropdown(1);">
                        <div class="tecdoc-selector__row" x-show="dropdownIndex !== 1">
                            <div class="tecdoc-selector__step">1</div>
                            <div class="tecdoc-selector__label" x-text="manufacturerPreview ? manufacturerPreview : '{l s='Select manufacturer' d='Modules.Itptecdoc.Shop'}'">{l s='Select manufacturer' d='Modules.Itptecdoc.Shop'}</div>
                        </div>

                        <div class="tecdoc-selector__row tecdoc-selector__row--search" x-show="dropdownIndex === 1" @click.stop x-cloak>
                            <input type="text" class="tecdoc-selector__search tecdoc-selector__search-1" name="search" title="{l s='Search...' d='Modules.Itptecdoc.Shop'}" placeholder="{l s='Search...' d='Modules.Itptecdoc.Shop'}" x-ref="search" x-model="search">
                        </div>
                    </div>

                    <div class="tecdoc-selector__dropdown tecdoc-scroll" x-show="dropdownIndex === 1" x-anchor.bottom.offset.10="$refs.manufacturers" x-cloak>
                        <div class="tecdoc-selector__option tecdoc-selector__option--disabled tecdoc-loader" x-show="manufacturersLoading">
                            {l s='Loading' d='Modules.Itptecdoc.Shop'}
                        </div>

                        <div x-show="!manufacturersLoading">
                            <div class="tecdoc-selector__option" @click="manufacturerId = null; manufacturerPreview = null; dropdownIndex = null;" x-show="!search">{l s='Select manufacturer' d='Modules.Itptecdoc.Shop'}</div>

                            <template x-for="(manufacturer, index) in visibleManufacturers">
                                <div class="tecdoc-selector__option" @click="setManufacturer(manufacturer.id, manufacturer.name)">
                                    <div class="tecdoc-selector__value" x-text="manufacturer.name"></div>
                                    <div class="tecdoc-selector__chevron"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <input type="hidden" name="manufacturer_id" x-model="manufacturerId">
                </div>

                <div class="tecdoc-vehicle-search__selector tecdoc-selector" :class="{ 'tecdoc-selector--selected': modelSeriesId, 'tecdoc-selector--active': dropdownIndex === 2 }">
                    <div class="tecdoc-selector__field" x-ref="modelSeries" @click="toggleDropdown(2);">
                        <div class="tecdoc-selector__row" x-show="dropdownIndex !== 2">
                            <div class="tecdoc-selector__step">2</div>
                            <div class="tecdoc-selector__label" x-text="modelSeriesPreview ? modelSeriesPreview : '{l s='Select model' d='Modules.Itptecdoc.Shop'}'">{l s='Select model' d='Modules.Itptecdoc.Shop'}</div>
                        </div>

                        <div class="tecdoc-selector__row tecdoc-selector__row--search" x-show="dropdownIndex === 2" @click.stop x-cloak>
                            <input type="text" class="tecdoc-selector__search tecdoc-selector__search-2" name="search" title="{l s='Search...' d='Modules.Itptecdoc.Shop'}" placeholder="{l s='Search...' d='Modules.Itptecdoc.Shop'}" x-ref="search" x-model="search">
                        </div>
                    </div>

                    <div class="tecdoc-selector__dropdown tecdoc-scroll" x-show="dropdownIndex === 2" x-anchor.bottom.offset.10="$refs.modelSeries" x-cloak>
                        <div class="tecdoc-selector__option tecdoc-selector__option--disabled tecdoc-loader" x-show="modelSeriesLoading">
                            {l s='Loading' d='Modules.Itptecdoc.Shop'}
                        </div>

                        <div x-show="!modelSeriesLoading">
                            <div class="tecdoc-selector__option" @click="modelSeriesId = null; modelSeriesPreview = null; dropdownIndex = null;" x-show="!search">{l s='Select model' d='Modules.Itptecdoc.Shop'}</div>

                            <template x-for="(model, index) in visibleModelSeries">
                                <div class="tecdoc-selector__option" @click="setModelSeries(model.id, model.name)">
                                    <div class="tecdoc-selector__value" x-text="model.name"></div>
                                    <div class="tecdoc-selector__chevron"></div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <input type="hidden" name="model_series_id" x-model="modelSeriesId">
                </div>

                <div class="tecdoc-vehicle-search__selector tecdoc-selector" :class="{ 'tecdoc-selector--selected': vehicleId, 'tecdoc-selector--active': dropdownIndex === 3 }">
                    <div class="tecdoc-selector__field" x-ref="vehicles" @click="toggleDropdown(3);">
                        <div class="tecdoc-selector__row" x-show="dropdownIndex !== 3">
                            <div class="tecdoc-selector__step">3</div>
                            <div class="tecdoc-selector__label" x-text="vehiclePreview ? vehiclePreview : '{l s='Select vehicle' d='Modules.Itptecdoc.Shop'}'">{l s='Select vehicle' d='Modules.Itptecdoc.Shop'}</div>
                        </div>

                        <div class="tecdoc-selector__row tecdoc-selector__row--search" x-show="dropdownIndex === 3" @click.stop x-cloak>
                            <input type="text" class="tecdoc-selector__search tecdoc-selector__search-3" name="search" title="{l s='Search...' d='Modules.Itptecdoc.Shop'}" placeholder="{l s='Search...' d='Modules.Itptecdoc.Shop'}" x-ref="search" x-model="search">
                        </div>
                    </div>

                    <div class="tecdoc-selector__dropdown tecdoc-scroll" x-show="dropdownIndex === 3" x-anchor.bottom.offset.10="$refs.vehicles" x-cloak>
                        <div class="tecdoc-selector__option tecdoc-selector__option--disabled tecdoc-loader" x-show="vehiclesLoading">
                            {l s='Loading' d='Modules.Itptecdoc.Shop'}
                        </div>

                        <div x-show="!vehiclesLoading">
                            <div class="tecdoc-selector__option" @click="vehicleId = null; vehiclePreview = null; dropdownIndex = null;" x-show="!search">{l s='Select vehicle' d='Modules.Itptecdoc.Shop'}</div>

                            <template x-for="(vehicle, index) in visibleVehicles">
                                <div class="tecdoc-selector__option" @click="setVehicle(vehicle.id, vehicle.description)">
                                    <div class="tecdoc-selector__column">
                                        <div class="tecdoc-selector__value" x-text="vehicle.description"></div>
                                        <div class="tecdoc-selector__sub-value" x-text="vehicle.fuelType + ' - ' + vehicle.horsePowerFrom + ' hp' + ' / ' + vehicle.kiloWattsFrom + ' kw';"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    <input type="hidden" name="vehicle_id" x-model="vehicleId">
                </div>

                <button :disabled="!manufacturerId && !modelSeriesId && !vehicleId" class="tecdoc-vehicle-search__button tecdoc-button tecdoc-button--search">{l s='Search' d='Modules.Itptecdoc.Shop'}</button>
            </div>
        </form>

        <div class="tecdoc-vehicle-search__backdrop" x-show="dropdownIndex" @click="dropdownIndex = null" x-transition.opacity x-cloak></div>
    </div>
{/if}

