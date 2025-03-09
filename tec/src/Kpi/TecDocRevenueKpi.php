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

namespace ItPremium\TecDoc\Kpi;

use Doctrine\DBAL\Driver\Exception;
use ItPremium\TecDoc\Service\StatisticService;
use PrestaShop\PrestaShop\Core\Kpi\KpiInterface;
use PrestaShop\PrestaShop\Core\Localization\Exception\LocalizationException;
use Symfony\Contracts\Translation\TranslatorInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class TecDocRevenueKpi implements KpiInterface
{
    /**
     * TecDocRevenueKpi constructor.
     *
     * @param TranslatorInterface $translator
     * @param StatisticService $statisticService
     */
    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly StatisticService $statisticService,
    ) {
    }

    /**
     * @return string
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     * @throws LocalizationException
     */
    public function render(): string
    {
        $dateFrom = date('Y-01-01');
        $dateTo = date('Y-12-31');

        $statisticData = $this
            ->statisticService
            ->getStatisticData($dateFrom, $dateTo);

        $helper = new \HelperKpi();
        $helper->id = 'box-tecdoc-revenue';
        $helper->icon = 'equalizer';
        $helper->color = 'color1';
        $helper->title = $this->translator->trans('TecDoc Revenue', [], 'Modules.Itptecdoc.Admin');
        $helper->subtitle = date('Y');
        $helper->value = $this->translator->trans('%s% tax excl.', ['%s%' => $statisticData->getRevenueTaxExclFormatted()], 'Modules.Itptecdoc.Admin');

        return $helper->generate();
    }
}
