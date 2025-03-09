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

/* Import setup */
jQuery(function ($) {
    let entityInput = $('select[name="entity"]');
    let methodInput = $('select[name="method"]');

    toggleImportEntityFields();
    toggleImportMethodFields();

    $(entityInput).on('change', function () {
        toggleImportEntityFields();
    });

    $(methodInput).on('change', function () {
        toggleImportMethodFields();
    });

    function toggleImportEntityFields() {
        let importEntityValue = parseInt(entityInput.val());
        let referenceSuffixFormGroup = $('input[name="reference_suffix"]').parents('div.form-group');
        let referencePostfixFormGroup = $('input[name="reference_postfix"]').parents('div.form-group');

        if (importEntityValue === 0) {
            referenceSuffixFormGroup.show();
            referencePostfixFormGroup.show();
        } else {
            referenceSuffixFormGroup.hide();
            referencePostfixFormGroup.hide();
        }
    }

    function toggleImportMethodFields() {
        let importMethodValue = parseInt(methodInput.val());
        let fileFormGroup = $('input[name="file"]').parents('div.form-group');
        let fileUrlFormGroup = $('input[name="file_url"]').parents('div.form-group');
        let ftpHostFormGroup = $('input[name="ftp_host"]').parents('div.form-group');
        let ftpPortFormGroup = $('input[name="ftp_port"]').parents('div.form-group');
        let ftpUsernameFormGroup = $('input[name="ftp_username"]').parents('div.form-group');
        let ftpPasswordFormGroup = $('input[name="ftp_password"]').parents('div.form-group');
        let ftpFileFormGroup = $('input[name="ftp_file"]').parents('div.form-group');

        if (importMethodValue === 1) {
            fileFormGroup.show();
        } else {
            fileFormGroup.hide();
        }

        if (importMethodValue === 2) {
            fileUrlFormGroup.show();
        } else {
            fileUrlFormGroup.hide();
        }

        if (importMethodValue === 3 || importMethodValue === 4) {
            ftpHostFormGroup.show();
            ftpPortFormGroup.show();
            ftpUsernameFormGroup.show();
            ftpPasswordFormGroup.show();
            ftpFileFormGroup.show();
        } else {
            ftpHostFormGroup.hide();
            ftpPortFormGroup.hide();
            ftpUsernameFormGroup.hide();
            ftpPasswordFormGroup.hide();
            ftpFileFormGroup.hide();
        }
    }
});

/* Import mapping settings */
jQuery(function ($) {
    let rowsToSkipInput = $('input[name="rows_to_skip"]');

    $(rowsToSkipInput).on('change', function () {
        this.form.submit();
    });
});

/* Import */
jQuery(function ($) {
    let importBody = $('.tecdoc-import-body');
    let importId = importBody.data('id');
    let importRowsToSkip = importBody.data('rows-to-skip');
    let importUrl = importBody.data('import-url');
    let limit = importBody.data('limit');
    let progressBar = importBody.find('.import-progress-bar');
    let totalRows = importBody.data('total-rows');
    let totalRecordsProcessed = 0;
    let totalTruncatedRecords = 0;
    let totalWarnings = 0;
    let totalErrors = 0;
    let importError = false;

    importBody.on('click', '.import-start', function () {
        runImport();
    });

    async function runImport() {
        await importStart();

        await importRecords()
            .then(function () {
                updateSuccessAlert();
                updateProgressBarSuccess();
            })
            .catch((error) => {
                importError = true;

                updateErrorAlert(error.statusText);
                updateProgressBarError();
            });

        await updateImportStatus();
    }

    async function importStart() {
        importBody.find('.import-progress').removeClass('hidden');
        importBody.find('.panel-footer').remove();

        return await $.ajax({
            url: importUrl,
            type: 'POST',
            data: {
                action: 'importStart',
                id_tecdoc_import: importId,
                ajax: true,
            },
            success: function (result) {
                totalTruncatedRecords += result.truncated_records;

                updateImportStat('.total-truncated-records', totalTruncatedRecords);
            },
        });
    }

    async function importRecords() {
        let totalPages = Math.ceil(totalRows / limit);

        for (let page = 1; page <= totalPages && !importError; page++) {
            let offset = limit * (page - 1);

            /** Skipping rows */
            if (!offset) {
                offset = importRowsToSkip;
            }

            await $.ajax({
                url: importUrl,
                type: 'POST',
                data: {
                    action: 'importRecords',
                    id_tecdoc_import: importId,
                    offset: offset,
                    ajax: true,
                },
                success: function (result) {
                    updateImportBodyState(result);
                    updateProgressBar(Math.floor(page / totalPages * 100));
                }
            });
        }
    }

    async function updateImportStatus() {
        return await $.ajax({
            url: importUrl,
            type: 'POST',
            data: {
                action: 'updateImportStatus',
                id_tecdoc_import: importId,
                total_imported: totalRecordsProcessed,
                total_warnings: totalWarnings,
                total_errors: totalErrors,
                import_error: +importError,
                ajax: true,
            }
        });
    }

    function updateImportBodyState(result) {
        result.warnings.forEach(function (warning) {
            updateInfoAlert(warning);
        });

        result.errors.forEach(function (error) {
            updateErrorAlert(error);
        });

        totalRecordsProcessed += result.total_records;
        totalWarnings += result.warnings.length;
        totalErrors += result.errors.length;

        updateImportStat('.total-records-processed', totalRecordsProcessed + '/' + totalRows);
        updateImportStat('.total-warnings', totalWarnings);
        updateImportStat('.total-errors', totalErrors);
    }

    function updateImportStat(className, value) {
        importBody.find(className).children('.import-stat-value').text(value);
    }

    function updateSuccessAlert() {
        importBody.find('.import-alert-success').removeClass('hidden');
    }

    function updateInfoAlert(message) {
        importBody.find('.import-alert-info').removeClass('hidden').append('<p>' + message + '</p>');
    }

    function updateErrorAlert(message) {
        importBody.find('.import-alert-error').removeClass('hidden').append('<p>' + message + '</p>');
    }

    function updateProgressBarSuccess() {
        progressBar.addClass('progress-bar-success');
    }

    function updateProgressBarError() {
        progressBar.addClass('progress-bar-danger');
    }

    function updateProgressBar(progress) {
        progressBar.css('width', progress + '%').text(progress + '%');
    }
});