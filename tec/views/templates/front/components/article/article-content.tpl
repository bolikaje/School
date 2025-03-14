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

<div class="tecdoc-article__content">
    <h3 class="tecdoc-article__title" itemprop="name">{$article->getName()}</h3>

    <div class="tecdoc-article__metas">
        <div class="tecdoc-article__meta tecdoc-article__meta--brand">
            <div class="tecdoc-article__meta-title">{l s='Brand' d='Modules.Itptecdoc.Shop'}:</div>
            <div class="tecdoc-article__meta-value" itemprop="brand" itemscope itemtype="http://schema.org/Brand">{$article->brandName}</div>
        </div>

        <div class="tecdoc-article__meta tecdoc-article__meta--reference">
            <div class="tecdoc-article__meta-title">{l s='Reference' d='Modules.Itptecdoc.Shop'}:</div>
            <div class="tecdoc-article__meta-value">{$article->reference}</div>
        </div>
    </div>

    {hook h='displayArticleContentAfter'}
</div>