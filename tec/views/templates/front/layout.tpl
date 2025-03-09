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

{extends file=$layout}

{block name="content"}
    <div class="tecdoc-page">
        {block name='page_header_container'}
            {block name='tecdoc_page_header_container'}
                <section class="tecdoc-page__header tecdoc-page-header">
                    <div class="tecdoc-page-header__column">
                        {block name='tecdoc_page_title'}
                            <h3 class="tecdoc-page-header__heading tecdoc-heading">{$smarty.block.child}</h3>
                        {/block}

                        {block name='tecdoc_page_subtitle' hide}
                            <div class="tecdoc-page-header__subheading">{$smarty.block.child}</div>
                        {/block}
                    </div>

                    {block name='tecdoc_page_title_after' hide}
                        <div class="tecdoc-page-header__column">{$smarty.block.child}</div>
                    {/block}
                </section>
            {/block}
        {/block}

        {block name='page_content_container'}
            <section class="tecdoc-page__content">
                {block name='tecdoc_page_content'}{/block}
            </section>
        {/block}
    </div>
{/block}