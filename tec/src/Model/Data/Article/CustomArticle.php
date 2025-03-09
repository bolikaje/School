<?php

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

declare(strict_types=1);

namespace ItPremium\TecDoc\Model\Data\Article;

use ItPremium\TecDoc\Enum\ArticleType;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class CustomArticle extends Article
{
    /**
     * @var ArticleType
     */
    protected ArticleType $articleType = ArticleType::CUSTOM_ARTICLE;

    /**
     * CustomArticle constructor.
     *
     * @param int $id
     * @param string $brandName
     * @param string $reference
     * @param ?string $name
     * @param bool $oem
     */
    public function __construct(
        /** @var int */
        public readonly int $id,

        /** @var string */
        public string $brandName,

        /** @var string */
        public string $reference,

        /** @var ?string */
        public readonly ?string $name,

        /** @var bool */
        public bool $oem,
    ) {
        parent::__construct(0, $this->brandName, $this->reference);
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param bool $fallback
     *
     * @return string
     */
    public function getName(bool $fallback = true): string
    {
        return $this->name ?: ($fallback ? $this->getAlternativeName() : '');
    }

    /**
     * @return string
     */
    public function getCoverImage(): string
    {
        $brandSlug = $this->getBrandSlug();
        $directory = 'itp_tecdoc/views/img/custom/';

        if (file_exists(_PS_MODULE_DIR_ . $directory . $brandSlug . '.png')) {
            return _MODULE_DIR_ . $directory . $brandSlug . '.png';
        }

        return _MODULE_DIR_ . 'itp_tecdoc/views/img/no-image.jpg';
    }

    /**
     * @return string
     */
    public function getReferenceSlug(): string
    {
        return \Tools::str2url($this->reference);
    }

    /**
     * @param ?int $langId
     *
     * @return string
     *
     * @throws \PrestaShopException
     */
    public function getLink(?int $langId = null): string
    {
        if (!$langId) {
            $langId = \Context::getContext()->language->id;
        }

        $dispatcher = \Dispatcher::getInstance();

        $params = [
            'id_custom_article' => $this->id,
        ];

        if ($dispatcher->hasKeyword('module-itp_tecdoc-customArticle', $langId, 'article_slug')) {
            $params['article_slug'] = $this->getArticleSlug();
        }

        if ($dispatcher->hasKeyword('module-itp_tecdoc-customArticle', $langId, 'brand_slug')) {
            $params['brand_slug'] = $this->getBrandSlug();
        }

        if ($dispatcher->hasKeyword('module-itp_tecdoc-customArticle', $langId, 'reference_slug')) {
            $params['reference_slug'] = $this->getReferenceSlug();
        }

        return \Context::getContext()->link->getModuleLink('itp_tecdoc', 'customArticle', $params, true, $langId);
    }
}
