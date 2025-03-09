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

<div class="tecdoc-article__images">
    <a href="{$article->getCoverImage()}" class="tecdoc-article__cover-image" data-fslightbox="gallery" data-type="image" target="_blank">
        <img class="tecdoc-article__image" src="{$article->getCoverImage()}" alt="{$article->getName()}" title="{$article->getName()}" draggable="false">
    </a>

    {if $article->images->count() > 1}
        <div class="tecdoc-article__thumbnails">
            <div class="tecdoc-article__swiper swiper" x-data="tecdocThumbnails()">
                <div class="tecdoc-article__swiper-wrapper swiper-wrapper">
                    {foreach from=$article->images item=$image key=$i}
                        {if $image->getImageUrl() != $article->getCoverImage()}
                            <div class="tecdoc-article__swiper-slide swiper-slide">
                                <a href="{$image->getImageUrl()}" class="tecdoc-article__thumbnail" data-fslightbox="gallery" data-type="image" target="_blank">
                                    <img class="tecdoc-article__image" src="{$image->getImageUrl()}" alt="{$article->getName()}" title="{$article->getName()}">
                                </a>
                            </div>
                        {/if}
                    {/foreach}
                </div>
            </div>
        </div>
    {/if}
</div>