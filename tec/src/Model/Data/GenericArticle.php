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

namespace ItPremium\TecDoc\Model\Data;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class GenericArticle
{
    /**
     * GenericArticle constructor.
     *
     * @param int $id
     * @param ?string $assemblyGroup
     * @param ?string $designation
     * @param ?string $masterDesignation
     * @param ?string $usageDesignation
     */
    public function __construct(
        /** @var int */
        public readonly int $id,
        public readonly ?string $assemblyGroup,
        public readonly ?string $designation,
        public readonly ?string $masterDesignation,
        public readonly ?string $usageDesignation,
    ) {
    }

    public function getGenericArticleSlug(): string
    {
        return \Tools::str2url($this->designation);
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

        $params = [
            'generic_article_id' => $this->id,
        ];

        $dispatcher = \Dispatcher::getInstance();

        if ($dispatcher->hasKeyword('module-itp_tecdoc-genericArticle', $langId, 'generic_article_slug')) {
            $params['generic_article_slug'] = $this->getGenericArticleSlug();
        }

        return \Context::getContext()->link->getModuleLink('itp_tecdoc', 'genericArticle', $params, true, $langId);
    }
}
