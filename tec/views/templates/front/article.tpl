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

{block name='tecdoc_page_header_container'}{/block}

{block name='tecdoc_page_content'}
    <div class="tecdoc-article tecdoc-article--full" x-data itemscope itemtype="https://schema.org/Product">
        {include file='module:itp_tecdoc/views/templates/front/components/article/article-brand-information.tpl'}

        <div class="tecdoc-article__row">
            {include file='module:itp_tecdoc/views/templates/front/components/article/article-images.tpl'}
            {include file='module:itp_tecdoc/views/templates/front/components/article/article-content.tpl'}
            {include file='module:itp_tecdoc/views/templates/front/components/article/article-availability.tpl'}
        </div>
    </div>

    {include file='module:itp_tecdoc/views/templates/front/components/article/article-tabs.tpl'}

    {if isset($allow_availability_requests) and $allow_availability_requests and $article->availability->isEmpty()}
        {include file='module:itp_tecdoc/views/templates/front/components/article/article-availability-request-modal.tpl'}
    {/if}
{/block}