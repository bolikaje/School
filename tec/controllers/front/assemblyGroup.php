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

use CuyZ\Valinor\Mapper\MappingError;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Exception\GuzzleException;
use ItPremium\TecDoc\Api\Exception\TecDocApiException;
use ItPremium\TecDoc\Api\Type\AssemblyGroupFacetOptionsType;
use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Enum\AssemblyGroupType;
use ItPremium\TecDoc\Enum\LinkingTargetType;
use ItPremium\TecDoc\Enum\Orientation;
use ItPremium\TecDoc\Model\Data\AssemblyGroup;
use ItPremium\TecDoc\Model\Data\LinkageTarget\LinkageTargetDetails;
use ItPremium\TecDoc\Model\Data\Manufacturer;
use ItPremium\TecDoc\Model\Data\ModelSeries;
use ItPremium\TecDoc\Model\Query\GetArticlesQuery;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Itp_TecdocAssemblyGroupModuleFrontController extends TecDocArticleListingFrontController
{
    /**
     * @var int
     */
    private $maxAllowedArticles = 10000;

    /**
     * @var Manufacturer
     */
    private $manufacturer;

    /**
     * @var ModelSeries
     */
    private $modelSeries;

    /**
     * @var LinkageTargetDetails
     */
    private $vehicle;

    /**
     * @var AssemblyGroup
     */
    private $assemblyGroup;

    /**
     * @var bool
     */
    private $showAdditionalRequirements = false;

    /**
     * @throws GuzzleException
     * @throws MappingError
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws TecDocApiException
     * @throws Symfony\Component\Cache\Exception\CacheException
     * @throws Exception
     */
    public function init()
    {
        parent::init();

        $assemblyGroupId = (int) Tools::getValue('assembly_group_id');
        $getArticlesQuery = new GetArticlesQuery();

        if ($vehicleId = (int) Tools::getValue('vehicle_id')) {
            $this->vehicle = $this
                ->tecdoc
                ->vehicles()
                ->getVehicleById($vehicleId, $this->linkingTargetType);

            if (!Validate::isLoadedObject($this->vehicle)) {
                exit($this->renderNotFound());
            }

            $this->manufacturer = $this
                ->tecdoc
                ->manufacturers()
                ->getManufacturerById($this->vehicle->manufacturerId, $this->linkingTargetType);

            if (!Validate::isLoadedObject($this->manufacturer) or !$this->manufacturer->active) {
                exit($this->renderNotFound());
            }

            $this->modelSeries = $this
                ->tecdoc
                ->modelSeries()
                ->getModelSeriesById($this->manufacturer->id, $this->vehicle->modelSeriesId, $this->linkingTargetType);

            if (!Validate::isLoadedObject($this->modelSeries)) {
                exit($this->renderNotFound());
            }

            $getArticlesQuery
                ->setLinkageTargetId($vehicleId)
                ->setLinkageTargetType($this->linkingTargetType->value);

            $this->context->smarty->assign([
                'manufacturer' => $this->manufacturer,
                'show_manufacturers_logo' => Configuration::get(ConfigurationConstant::TECDOC_SHOW_MANUFACTURERS_LOGO),
            ]);
        }

        $this->assemblyGroup = $this
            ->tecdoc
            ->assemblyGroups()
            ->getAssemblyGroupById($assemblyGroupId);

        if (!Validate::isLoadedObject($this->assemblyGroup)) {
            exit($this->renderNotFound());
        }

        $this->canonicalRedirection($this->assemblyGroup->getLink($this->vehicle));

        if ($this->assemblyGroup->count > $this->maxAllowedArticles and !$this->assemblyGroup->subgroups->isEmpty()) {
            $this->showAdditionalRequirements = true;
        }

        if ($this->showAdditionalRequirements) {
            $this->articles = new ArrayCollection();
        } else {
            $getArticlesQuery->setAssemblyGroupNodeIds($this->assemblyGroup->id);

            $this->articles = $this
                ->tecdoc
                ->articles()
                ->getArticles($getArticlesQuery, true);
        }

        $this->context->smarty->assign([
            'assembly_group' => $this->assemblyGroup,
            'vehicle' => $this->vehicle,
        ]);

        if (Configuration::get(ConfigurationConstant::TECDOC_SHOW_NESTED_ASSEMBLY_GROUPS_ON_ARTICLE_LISTING)) {
            $assemblyGroupFacetOptionType = (new AssemblyGroupFacetOptionsType())
                ->setAssemblyGroupType($this->assemblyGroup->type->value);

            $getArticlesQuery = (new GetArticlesQuery())
                ->setAssemblyGroupFacetOptions($assemblyGroupFacetOptionType);

            $assemblyGroups = $this
                ->tecdoc
                ->assemblyGroups()
                ->getNestedAssemblyGroups($getArticlesQuery);

            $cTreePath = $this->getTreePath(
                $assemblyGroupId,
                $this->tecdoc->assemblyGroups()->getAssemblyGroups($getArticlesQuery)
            );

            $topParentId = end($cTreePath) ?: $this->assemblyGroup->id;
            $iterator = $assemblyGroups->getIterator();

            $iterator->uasort(function (AssemblyGroup $a, AssemblyGroup $b) use ($topParentId) {
                if ($a->id == $topParentId) {
                    return -1;
                }

                if ($b->id == $topParentId) {
                    return 1;
                }

                return $a->id <=> $b->id;
            });

            $this->context->smarty->assign([
                'assembly_groups' => new ArrayCollection(iterator_to_array($iterator)),
                'c_tree_path' => $cTreePath,
            ]);
        }
    }

    /**
     * @return void
     *
     * @throws PrestaShopException
     * @throws Exception
     */
    public function initContent(): void
    {
        parent::initContent();

        if ($this->showAdditionalRequirements) {
            $linkingTargetType = LinkingTargetType::tryFrom($this->assemblyGroup->type->value)
                ?: LinkingTargetType::PASSENGER;

            $vehicleSearchWidget = $this
                ->tecdoc
                ->widgets()
                ->getVehicleSearchWidget(Orientation::VERTICAL, false, $linkingTargetType);

            $this->context->smarty->assign([
                'show_vehicle_search' => !$this->vehicle and $this->assemblyGroup->type != AssemblyGroupType::UNIVERSAL,
                'vehicle_search_widget' => $vehicleSearchWidget,
            ]);

            $this->setTemplate('module:' . $this->module->name . '/views/templates/front/assembly-group.tpl');
        }
    }

    /**
     * @return array
     *
     * @throws PrestaShopException
     */
    public function getBreadcrumbLinks(): array
    {
        $breadcrumb = parent::getBreadcrumbLinks();

        if ($this->vehicle) {
            $breadcrumb['links'][] = [
                'title' => $this->manufacturer->name,
                'url' => $this->manufacturer->getLink($this->linkingTargetType),
            ];

            $breadcrumb['links'][] = [
                'title' => $this->modelSeries->name,
                'url' => $this->modelSeries->getLink(),
            ];

            $breadcrumb['links'][] = [
                'title' => $this->vehicle->description,
                'url' => $this->vehicle->getLink(),
            ];
        }

        if (!$this->notFound) {
            $breadcrumb['links'][] = [
                'title' => $this->assemblyGroup->name,
                'url' => false,
            ];
        }

        return $breadcrumb;
    }

    /**
     * @return array
     */
    public function getTemplateVarPage(): array
    {
        $page = parent::getTemplateVarPage();

        if (!$this->notFound) {
            if ($this->vehicle) {
                $title = Configuration::get(ConfigurationConstant::TECDOC_META_TITLE_FOR_ARTICLES, $this->context->language->id)
                    ?: $this->assemblyGroup->name;

                $description = Configuration::get(ConfigurationConstant::TECDOC_META_DESCRIPTION_FOR_ARTICLES, $this->context->language->id);
            } else {
                $title = Configuration::get(ConfigurationConstant::TECDOC_META_TITLE_FOR_ASSEMBLY_GROUP, $this->context->language->id)
                    ?: $this->assemblyGroup->name;

                $description = Configuration::get(ConfigurationConstant::TECDOC_META_DESCRIPTION_FOR_ASSEMBLY_GROUP, $this->context->language->id);
            }

            $page['body_classes']['tecdoc-assembly-group-page'] = true;
            $page['title'] = $this->prepareMetaData($title);
            $page['meta']['title'] = $page['title'];
            $page['meta']['description'] = $this->prepareMetaData($description);
        }

        return $page;
    }

    /**
     * @return string
     *
     * @throws PrestaShopException
     */
    public function getCanonicalURL(): string
    {
        if ($this->notFound) {
            return '';
        }

        return $this->assemblyGroup->getLink($this->vehicle);
    }

    /**
     * @param $assemblyGroupId
     * @param $assemblyGroups
     *
     * @return array
     */
    private function getTreePath($assemblyGroupId, $assemblyGroups): array
    {
        $ids = [];

        foreach ($assemblyGroups as $assemblyGroup) {
            if ($assemblyGroup->id === $assemblyGroupId) {
                if ($assemblyGroup->parentNodeId) {
                    $ids[] = $assemblyGroup->parentNodeId;

                    array_push($ids, ...$this->getTreePath($assemblyGroup->parentNodeId, $assemblyGroups));
                }
            }
        }

        return $ids;
    }

    /**
     * @param string $string
     *
     * @return string
     */
    protected function prepareMetaData(string $string): string
    {
        if ($this->vehicle) {
            $search = [
                '%manufacturerName%',
                '%modelSeriesName%',
                '%vehicleName%',
                '%assemblyGroupName%',
            ];

            $replace = [
                $this->manufacturer->name,
                $this->modelSeries->name,
                $this->vehicle->description,
                $this->assemblyGroup->name,
            ];
        } else {
            $search = [
                '%assemblyGroupName%',
            ];

            $replace = [
                $this->assemblyGroup->name,
            ];
        }

        return str_replace($search, $replace, $string);
    }
}
