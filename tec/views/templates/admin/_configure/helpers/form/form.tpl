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

{extends file='helpers/form/form.tpl'}

{block name='input' append}
    {if $input.type == 'criteria_tags'}
    {literal}
        <script type="text/javascript">
            $().ready(function () {
                let input = document.getElementById('{/literal}{$input.name}{literal}');
                let data = {/literal}{$input.options.query|array_values|json_encode}{literal};

                const tagifyData = data.map(item => ({
                    value: item.{/literal}{$input.options.id}{literal},
                    name: item.{/literal}{$input.options.name}{literal},
                }));

                new Tagify(input, {
                    enforceWhitelist: true,
                    skipInvalid: true,
                    tagTextProp: 'name',
                    whitelist: tagifyData,
                    dropdown: {
                        closeOnSelect: false,
                        enabled: 0,
                        mapValueTo: 'name',
                        maxItems: 4000,
                        position: 'text',
                        searchKeys: ['name']
                    }
                });
            });
        </script>
    {/literal}
        {assign var='value_text' value=$fields_value[$input.name]}
        <input type="text"
               name="{$input.name}"
               id="{if isset($input.id)}{$input.id}{else}{$input.name}{/if}"
               value="{if isset($input.string_format) && $input.string_format}{$value_text|string_format:$input.string_format|escape:'html':'UTF-8'}{else}{$value_text|escape:'html':'UTF-8'}{/if}"
               class="{if isset($input.class)}{$input.class}{/if}" autocomplete="off"
               {if isset($input.placeholder) && $input.placeholder } placeholder="{$input.placeholder}"{/if}
        />
    {/if}
{/block}