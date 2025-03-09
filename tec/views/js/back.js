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

/* Widget setup */
jQuery(function ($) {
    let typeInput = $('select[name="type"]');

    toggleWidgetFields();

    $(typeInput).on('change', function () {
        toggleWidgetFields();
    });

    function toggleWidgetFields() {
        let typeInputValue = parseInt(typeInput.val());

        let showLinkageTargetTypesFormGroup = $('input[name="show_linkage_target_types"]').parents('div.form-group');
        let orientationFormGroup = $('select[name="orientation"]').parents('div.form-group');
        let assemblyGroupsFormGroup = $('select[name="assembly_groups[]"]').parents('div.form-group');
        let manufacturersFormGroup = $('select[name="manufacturers[]"]').parents('div.form-group');
        let brandsFormGroup = $('select[name="brands[]"]').parents('div.form-group');
        let customHtmlFormGroup = $('textarea[name^="custom_html"]').parents('div.form-group').parents('div.form-group');

        showLinkageTargetTypesFormGroup.toggle(typeInputValue === 1);
        orientationFormGroup.toggle(typeInputValue === 1);
        manufacturersFormGroup.toggle(typeInputValue === 2);
        brandsFormGroup.toggle(typeInputValue === 3);
        customHtmlFormGroup.toggle(typeInputValue === 5);
        assemblyGroupsFormGroup.toggle(typeInputValue === 6);
    }
});