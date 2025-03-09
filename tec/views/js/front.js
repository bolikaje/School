/**
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
 */

jQuery(function ($) {
    $('.tecdoc-grid--dropdownable').each(function () {
        let $grid = $(this);
        let $gridItems = $('.tecdoc-grid__item', $grid);

        $gridItems.on('click', function (e) {
            let $this = $(this);

            if($this.hasClass('tecdoc-grid__item--has-link')) {
                return;
            }

            if ($this.hasClass('tecdoc-grid__item--active')) {
                $this.removeClass('tecdoc-grid__item--active');
                return;
            }

            $this.siblings().removeClass('tecdoc-grid__item--active');
            $this.addClass('tecdoc-grid__item--active');
        });

        let itemsPerRow, updatedItemsPerRow;

        $(window).on('resize', function () {
            updatedItemsPerRow = parseInt($grid.outerWidth() / $gridItems.first().outerWidth());

            if (itemsPerRow !== updatedItemsPerRow) {
                itemsPerRow = updatedItemsPerRow;

                $gridItems.each(function (i) {
                    let $this = $(this);
                    let isFirstInRow = i % itemsPerRow === 0;
                    let isLastInRow = i % itemsPerRow === itemsPerRow - 1;
                    let dropdownOrder = parseInt(i / itemsPerRow) * itemsPerRow + itemsPerRow;

                    $this.css('order', i)
                    $this.toggleClass('tecdoc-grid__item--first-in-row', isFirstInRow)
                    $this.toggleClass('tecdoc-grid__item--last-in-row', isLastInRow)
                    $this.next('.tecdoc-grid__dropdown').css('order', dropdownOrder);
                });
            }

        });
    });

    $(window).trigger('resize');
});

document.addEventListener('alpine:init', () => {
    Alpine.data('tecdocArticles', () => {
        return {
            view: 0,

            sortArticles(sortOrder) {
                const parser = new URL(window.location);
                parser.searchParams.set('sort_order', sortOrder);
                window.location = parser.href;
            }
        };
    });

    Alpine.data('tecdocDropdown', (dropdownValue) => {
        return {
            dropdownPreview: null,
            dropdownValue: dropdownValue,
            expanded: false,

            init() {
                this.dropdownPreview = this.$refs.preview.innerText
            },

            selectOption(value) {
                this.dropdownPreview = this.$event.target.innerText
                this.dropdownValue = value;
            },

            trigger: {
                [':class']() {
                    return {'tecdoc-dropdown--expanded': this.expanded};
                },

                ['@click']() {
                    this.expanded = !this.expanded
                },

                ['@click.away']() {
                    this.expanded = false
                },
            },
        };
    });

    Alpine.data('tecdocAvailabilityRequestForm', () => {
        return {
            errors: {},
            message: null,
            loading: false,

            form: {
                product: '',
                qty: 1,
                email: '',
                comment: '',
                token: '',
            },

            init() {
                this.$watch('form.qty', value => value <= 0 && (this.form.qty = 1));
            },

            async submit() {
                this.errors = {};

                if (typeof grecaptcha != 'undefined') {
                    grecaptcha.ready(function () {
                        grecaptcha.execute(recaptchaSiteKey, {action: 'submit'}).then(function (token) {
                            this.form.token = token;
                        });
                    });
                }

                let parameters = {
                    'product': this.form.product,
                    'qty': this.form.qty,
                    'email': this.form.email,
                    'comment': this.form.comment,
                    'token': this.form.token,
                };

                this.loading = true;

                let response = await makeAjaxCall(this.$refs.form.action, 'makeAvailabilityRequest', parameters);

                this.loading = false;

                this.errors = await response.errors;
                this.message = await response.message;
            }
        };
    });

    Alpine.data('tecdocFacets', () => {
        return {
            reset() {
                this.$root.querySelectorAll('input[type=checkbox]').forEach(el => el.checked = false);
                this.$refs.form.submit();
            }
        };
    });

    Alpine.data('tecdocFacet', (facetOptions = [], expanded = false) => {
        return {
            expanded: expanded,
            facetOptions: facetOptions,
            search: null,

            init() {
                if(this.facetOptions.some(filter => filter.active)) {
                    this.expanded = true;
                }

                this.$watch('search', value => {
                    if (value) {
                        this.facetOptions = facetOptions.filter((facetOption) => facetOption.label.toLowerCase().indexOf(value.toLowerCase()) !== -1);
                    } else {
                        this.facetOptions = facetOptions;
                    }
                });
            }
        };
    });

    Alpine.data('tecdocManufacturers', (manufacturers) => {
        return {
            alphabeticalFilter: null,
            manufacturers: manufacturers,

            init() {
                this.$watch('alphabeticalFilter', value => {
                    if (value) {
                        this.manufacturers = manufacturers.filter((manufacturer) => manufacturer.name.startsWith(value));
                    } else {
                        this.manufacturers = manufacturers;
                    }
                });
            }
        };
    });

    Alpine.data('tecdocQuantityInput', () => {
        return {
            qty: null,
            minQty: null,
            maxQty: null,

            init() {
                let qtyInput = this.$refs.input;

                this.qty = qtyInput.min;
                this.minQty = this.qty;
                this.maxQty = qtyInput.max;

                this.$watch('qty', value => this.validateInput(value));
            },

            validateInput(value) {
                if (value < this.minQty) {
                    this.qty = this.minQty;
                }

                if (value > this.maxQty) {
                    this.qty = this.maxQty;
                }
            },
        };
    });

    Alpine.data('tecdocVehicleSearch', (linkingTargetType) => {
        return {
            dropdownIndex: null,
            linkingTargetType: linkingTargetType,
            manufacturerId: null,
            manufacturerPreview: null,
            manufacturers: {},
            manufacturersLoading: false,
            modelSeries: {},
            modelSeriesId: null,
            modelSeriesLoading: false,
            modelSeriesPreview: null,
            search: '',
            vehicleId: null,
            vehiclePreview: null,
            vehicles: {},
            vehiclesLoading: false,
            visibleManufacturers: {},
            visibleModelSeries: {},
            visibleVehicles: {},

            init() {
                this.loadManufacturers();

                this.$watch('linkingTargetType', () => this.reset());
                this.$watch('dropdownIndex', () => this.search = '')
                this.$watch('manufacturerId', () => this.loadModelSeries());
                this.$watch('modelSeriesId', () => this.loadVehicles());
                this.$watch('search', () => this.performSearch());
            },

            reset() {
                this.manufacturerId = null;
                this.manufacturerPreview = null;
                this.manufacturers = {};
                this.modelSeries = {};
                this.modelSeriesId = null;
                this.modelSeriesPreview = null;
                this.vehicleId = null;
                this.vehiclePreview = null;
                this.vehicles = {};
                this.visibleManufacturers = {};
                this.visibleModelSeries = {};
                this.visibleVehicles = {};

                this.loadManufacturers();
            },

            toggleDropdown(index) {
                this.dropdownIndex !== index
                    ? this.dropdownIndex = index
                    : this.dropdownIndex = null;

                if (this.dropdownIndex) {
                    this.performSearch();

                    this.$nextTick(() => {
                        this.$el.querySelector('input[name=search]').focus();
                    });
                }
            },

            performSearch() {
                if (this.dropdownIndex === 1) {
                    this.visibleManufacturers = this.search.length
                        ? Object.values(this.manufacturers).filter((manufacturer) => manufacturer.name.toLowerCase().indexOf(this.search.toLowerCase()) !== -1)
                        : this.manufacturers;
                }

                if (this.dropdownIndex === 2) {
                    this.visibleModelSeries = this.search.length
                        ? Object.values(this.modelSeries).filter((modelSeries) => modelSeries.name.toLowerCase().indexOf(this.search.toLowerCase()) !== -1)
                        : this.modelSeries;
                }

                if (this.dropdownIndex === 3) {
                    this.visibleVehicles = this.search.length
                        ? Object.values(this.vehicles).filter((vehicle) => vehicle.description.toLowerCase().indexOf(this.search.toLowerCase()) !== -1)
                        : this.vehicles;
                }
            },

            setManufacturer(manufacturerId, manufacturerPreview) {
                setTimeout(() => {
                    document.cookie = 'tecdocVehicleSearchManufacturerId=' + manufacturerId;

                    this.manufacturerId = manufacturerId;
                    this.manufacturerPreview = manufacturerPreview;
                    this.dropdownIndex++;

                    this.$nextTick(() => {
                        document.querySelector(`.tecdoc-selector__search-${this.dropdownIndex}`).focus();
                    });
                }, 300)
            },

            setModelSeries(modelSeriesId, modelSeriesPreview) {
                setTimeout(() => {
                    document.cookie = 'tecdocVehicleSearchModelSeriesId=' + modelSeriesId;

                    this.modelSeriesId = modelSeriesId;
                    this.modelSeriesPreview = modelSeriesPreview;
                    this.dropdownIndex++;

                    this.$nextTick(() => {
                        document.querySelector(`.tecdoc-selector__search-${this.dropdownIndex}`).focus();
                    });
                }, 300)
            },

            setVehicle(vehicleId, vehiclePreview) {
                setTimeout(() => {
                    document.cookie = 'tecdocVehicleSearchVehicleId=' + vehicleId;

                    this.vehicleId = vehicleId;
                    this.vehiclePreview = vehiclePreview;
                    this.dropdownIndex = null;

                    this.$nextTick(() => {
                        this.$refs.form.submit();
                    });
                }, 300)
            },

            async loadManufacturers() {
                let parameters = {
                    'linking_target_type': this.linkingTargetType,
                };

                this.manufacturersLoading = true;
                let response = await makeAjaxCall(this.$refs.form.action, 'manufacturers', parameters);

                this.manufacturers = await response.manufacturers;
                this.visibleManufacturers = this.manufacturers;
                this.manufacturersLoading = false
            },

            async loadModelSeries() {
                let parameters = {
                    'linking_target_type': this.linkingTargetType,
                    'manufacturer_id': this.manufacturerId,
                };

                this.modelSeriesLoading = true;
                let response = await makeAjaxCall(this.$refs.form.action, 'modelSeries', parameters);

                this.modelSeries = await response.model_series;
                this.visibleModelSeries = this.modelSeries;
                this.modelSeriesLoading = false
            },

            async loadVehicles() {
                let parameters = {
                    'linking_target_type': this.linkingTargetType,
                    'manufacturer_id': this.manufacturerId,
                    'model_series_id': this.modelSeriesId,
                };

                this.vehiclesLoading = true;
                let response = await makeAjaxCall(this.$refs.form.action, 'vehicles', parameters);

                this.vehicles = await response.vehicles;
                this.visibleVehicles = this.vehicles;
                this.vehiclesLoading = false;
            },
        };
    });
});

async function makeAjaxCall(url, action, parameters) {
    const formData = new FormData();
    formData.append('ajax', 'true');
    formData.append('action', action);

    for (const [key, value] of Object.entries(parameters)) {
        formData.append(key, value);
    }

    return await fetch(url, {
        method: 'POST',
        body: formData
    }).then(result => result.json());
}

function getCookie(name) {
    let matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}