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

namespace ItPremium\TecDoc\Service;

use CuyZ\Valinor\Mapper\MappingError;
use Doctrine\Common\Collections\Criteria;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver\Exception;
use GuzzleHttp\Exception\GuzzleException;
use ItPremium\TecDoc\Api\Exception\TecDocApiException;
use ItPremium\TecDoc\Entity\Doctrine\TecdocWidget;
use ItPremium\TecDoc\Enum\LinkingTargetType;
use ItPremium\TecDoc\Enum\Orientation;
use ItPremium\TecDoc\Enum\WidgetType;
use ItPremium\TecDoc\Model\Widget\AssemblyGroupsListWidget;
use ItPremium\TecDoc\Model\Widget\BrandsListWidget;
use ItPremium\TecDoc\Model\Widget\CustomHtmlWidget;
use ItPremium\TecDoc\Model\Widget\ManufacturersListWidget;
use ItPremium\TecDoc\Model\Widget\SearchFormWidget;
use ItPremium\TecDoc\Model\Widget\TecDocInsideWidget;
use ItPremium\TecDoc\Model\Widget\VehicleSearchWidget;
use ItPremium\TecDoc\Repository\WidgetRepository;
use Symfony\Component\Cache\Exception\CacheException;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class WidgetService
{
    /**
     * WidgetService constructor.
     *
     * @param AssemblyGroupService $assemblyGroupService
     * @param BrandService $brandService
     * @param ManufacturerService $manufacturerService
     * @param WidgetRepository $widgetRepository
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        private readonly AssemblyGroupService $assemblyGroupService,
        private readonly BrandService $brandService,
        private readonly ManufacturerService $manufacturerService,
        private readonly WidgetRepository $widgetRepository,
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * @param string $hookName
     * @param int $langId
     * @param int $shopId
     *
     * @return TecdocWidget[]
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws \PrestaShopDatabaseException
     */
    public function getWidgetsByHook(string $hookName, int $langId, int $shopId): array
    {
        $widgets = $this
            ->widgetRepository
            ->getWidgetsByHook($hookName, $langId, $shopId);

        return $this->prepareWidgets($widgets);
    }

    /**
     * @return array
     *
     * @throws Exception
     * @throws \Doctrine\DBAL\Exception
     */
    public function getAvailableHooks(): array
    {
        $qb = $this
            ->connection
            ->createQueryBuilder();

        $result = $qb->select('h.id_hook, h.name')
            ->from($this->dbPrefix . 'hook', 'h')
            ->where('h.name LIKE :display')
            ->setParameter('display', 'display%')
            ->orderBy('name', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        foreach ($result as $key => $hook) {
            if (preg_match('/admin/i', $hook['name']) or preg_match('/backoffice/i', $hook['name'])) {
                unset($result[$key]);
            }
        }

        return $result;
    }

    /**
     * @param TecdocWidget[] $widgets
     *
     * @return TecdocWidget[]
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     */
    private function prepareWidgets(array $widgets): array
    {
        foreach ($widgets as $widget) {
            $widgetType = WidgetType::from($widget->getType());

            $content = match ($widgetType) {
                WidgetType::ASSEMBLY_GROUPS_LIST => $this->getAssemblyGroupsWidget($widget->getAssemblyGroups()),
                WidgetType::SEARCH_FORM => $this->getSearchFormWidget(),
                WidgetType::VEHICLE_SEARCH => $this->getVehicleSearchWidget($widget->getOrientation(), $widget->getShowLinkageTargetTypes()),
                WidgetType::MANUFACTURERS_LIST => $this->getManufacturersListWidget($widget->getManufacturers()),
                WidgetType::BRANDS_LIST => $this->getBrandsListWidget($widget->getBrands()),
                WidgetType::TECDOC_INSIDE => $this->getTecDocInsideWidget(),
                WidgetType::CUSTOM_HTML => $this->getCustomHtmlWidget($widget->getCustomHtml()),
            };

            $widget->setContent($content);
        }

        return $widgets;
    }

    /**
     * @param string $assemblyGroupIds
     *
     * @return AssemblyGroupsListWidget
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws CacheException
     */
    private function getAssemblyGroupsWidget(string $assemblyGroupIds): AssemblyGroupsListWidget
    {
        $assemblyGroupIds = array_map(
            'intval',
            explode(',', $assemblyGroupIds)
        );

        $criteria = Criteria::create()
            ->where(Criteria::expr()->in('id', $assemblyGroupIds));

        $manufacturers = $this
            ->assemblyGroupService
            ->getAssemblyGroups()
            ->matching($criteria);

        return new AssemblyGroupsListWidget($manufacturers);
    }

    /**
     * @return SearchFormWidget
     */
    public function getSearchFormWidget(): SearchFormWidget
    {
        return new SearchFormWidget();
    }

    /**
     * @param Orientation $orientation
     * @param bool $showLinkageTargetTypes
     * @param LinkingTargetType $linkingTargetType
     *
     * @return VehicleSearchWidget
     */
    public function getVehicleSearchWidget(Orientation $orientation, bool $showLinkageTargetTypes, LinkingTargetType $linkingTargetType = LinkingTargetType::PASSENGER): VehicleSearchWidget
    {
        return new VehicleSearchWidget($orientation, $showLinkageTargetTypes, $linkingTargetType);
    }

    /**
     * @param string $manufacturerIds
     *
     * @return ManufacturersListWidget
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     */
    public function getManufacturersListWidget(string $manufacturerIds): ManufacturersListWidget
    {
        $manufacturerIds = array_map(
            'intval',
            explode(',', $manufacturerIds)
        );

        $criteria = Criteria::create()
            ->where(Criteria::expr()->in('id', $manufacturerIds));

        $manufacturers = $this
            ->manufacturerService
            ->getManufacturers()
            ->matching($criteria);

        return new ManufacturersListWidget($manufacturers);
    }

    /**
     * @param string $brandsIds
     *
     * @return BrandsListWidget
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws CacheException
     */
    public function getBrandsListWidget(string $brandsIds): BrandsListWidget
    {
        $brandsIds = array_map(
            'intval',
            explode(',', $brandsIds)
        );

        $criteria = Criteria::create()
            ->where(Criteria::expr()->in('id', $brandsIds));

        $brands = $this
            ->brandService
            ->getBrands()
            ->matching($criteria);

        return new BrandsListWidget($brands);
    }

    /**
     * @return TecDocInsideWidget
     */
    public function getTecDocInsideWidget(): TecDocInsideWidget
    {
        return new TecDocInsideWidget();
    }

    /**
     * @param string $customHtml
     *
     * @return CustomHtmlWidget
     */
    public function getCustomHtmlWidget(string $customHtml): CustomHtmlWidget
    {
        return new CustomHtmlWidget($customHtml);
    }
}
