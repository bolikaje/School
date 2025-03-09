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
use Doctrine\DBAL\Exception;
use Doctrine\ORM\NonUniqueResultException;
use GuzzleHttp\Exception\GuzzleException;
use ItPremium\TecDoc\Api\Exception\TecDocApiException;
use ItPremium\TecDoc\Constant\ConfigurationConstant;
use ItPremium\TecDoc\Constant\DatabaseConstant;
use ItPremium\TecDoc\Enum\ArticleType;
use ItPremium\TecDoc\Model\Data\Article\Article;
use ItPremium\TecDoc\Model\Data\Article\ArticleStock;
use ItPremium\TecDoc\Model\Data\Article\ArticleStockGroupDiscount;
use ItPremium\TecDoc\Utils\Helper;
use Symfony\Component\Cache\Exception\CacheException;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class ProductService
{
    /**
     * ProductService constructor.
     *
     * @param ArticleService $articleService
     * @param StockService $stockService
     * @param Connection $connection
     * @param string $dbPrefix
     */
    public function __construct(
        private readonly ArticleService $articleService,
        private readonly StockService $stockService,
        private readonly Connection $connection,
        private readonly string $dbPrefix,
    ) {
    }

    /**
     * @param int $tecdocSupplierId
     * @param int $tecdocBrandId
     * @param string $articleReference
     *
     * @return \Product|bool
     *
     * @throws Exception
     * @throws GuzzleException
     * @throws MappingError
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws TecDocApiException
     * @throws \Doctrine\DBAL\Driver\Exception
     * @throws CacheException
     */
    public function getTecDocArticleAsPrestaProduct(int $tecdocSupplierId, int $tecdocBrandId, string $articleReference): \Product|bool
    {
        $article = $this
            ->articleService
            ->getSingleArticle($tecdocBrandId, $articleReference);

        if (!$availability = $this->getAvailability($article, $tecdocSupplierId)) {
            return false;
        }

        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select('p.id_product')
            ->from($this->dbPrefix . 'product', 'p')
            ->leftJoin('p', $this->dbPrefix . DatabaseConstant::TECDOC_PRODUCT_TABLE, 'tp', 'p.id_product = tp.id_product')
            ->where('tp.id_tecdoc_supplier = :id_tecdoc_supplier')
            ->andWhere('tp.id_tecdoc_brand = :id_tecdoc_brand')
            ->andWhere('tp.article_reference = :article_reference')
            ->andWhere('tp.enforce_quantity_multiple = :enforce_quantity_multiple')
            ->setParameters([
                'id_tecdoc_supplier' => $tecdocSupplierId,
                'id_tecdoc_brand' => $tecdocBrandId,
                'article_reference' => $articleReference,
                'enforce_quantity_multiple' => $availability->enforceQuantityMultiple,
            ])
            ->execute()
            ->fetchAssociative();

        if ($result) {
            $product = $this->updatePrestaProductFromArticle((int) $result['id_product'], $availability);
        } else {
            $product = $this->createPrestaProductFromArticle($article, $availability);
        }

        return $this->processPrestaProductRelations($product, $availability);
    }

    /**
     * @param int $tecdocSupplierId
     * @param int $customArticleId
     *
     * @return \Product|bool
     *
     * @throws Exception
     * @throws GuzzleException
     * @throws MappingError
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws TecDocApiException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function getCustomArticleAsPrestaProduct(int $tecdocSupplierId, int $customArticleId): \Product|bool
    {
        $article = $this
            ->articleService
            ->getSingleCustomArticle($customArticleId);

        if (!$availability = $this->getAvailability($article, $tecdocSupplierId)) {
            return false;
        }

        $qb = $this->connection->createQueryBuilder();

        $result = $qb->select('p.id_product')
            ->from($this->dbPrefix . 'product', 'p')
            ->leftJoin('p', $this->dbPrefix . DatabaseConstant::TECDOC_CUSTOM_PRODUCT, 'tcp', 'p.id_product = tcp.id_product')
            ->where('tcp.id_tecdoc_supplier = :id_tecdoc_supplier')
            ->andWhere('tcp.id_custom_article = :id_custom_article')
            ->andWhere('tcp.enforce_quantity_multiple = :enforce_quantity_multiple')
            ->setParameters([
                'id_tecdoc_supplier' => $tecdocSupplierId,
                'id_custom_article' => $customArticleId,
                'enforce_quantity_multiple' => $availability->enforceQuantityMultiple,
            ])
            ->execute()
            ->fetchAssociative();

        if ($result) {
            $product = $this->updatePrestaProductFromArticle((int) $result['id_product'], $availability);
        } else {
            $product = $this->createPrestaProductFromArticle($article, $availability);
        }

        return $this->processPrestaProductRelations($product, $availability);
    }

    /**
     * @param Article $article
     * @param ArticleStock $availability
     *
     * @return \Product
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function createPrestaProductFromArticle(Article $article, ArticleStock $availability): \Product
    {
        $tecDocCategory = Helper::getTecDocCategory();

        $product = new \Product();
        $product->id_category_default = $tecDocCategory->id;
        $product->name = Helper::createMultiLangValue($article->getName());

        $product->link_rewrite = array_map(function ($v) {
            return \Tools::str2url($v);
        }, $product->name);

        $product->reference = $article->brandName . ' ' . $article->reference;
        $product->wholesale_price = $availability->prices->wholesalePrice;
        $product->price = $availability->prices->priceWithoutReductionsWithoutTax;
        $product->minimal_quantity = $availability->minimumOrderQuantity;
        $product->id_tax_rules_group = \Configuration::get(ConfigurationConstant::TECDOC_ID_TAX_RULES_GROUP);
        $product->visibility = 'none';

        if ($availability->weight) {
            $product->weight = $availability->weight;
        }

        $product->add();

        $product->addToCategories($tecDocCategory->id);

        match ($article->getType()) {
            ArticleType::TECDOC_ARTICLE => $product->markAsTecDocArticleProduct(
                $availability->tecdocSupplierId,
                $article->brandId,
                $article->reference,
                $availability->enforceQuantityMultiple
            ),
            ArticleType::CUSTOM_ARTICLE => $product->markAsCustomArticleProduct(
                $availability->tecdocSupplierId,
                $article->getId(),
                $availability->enforceQuantityMultiple
            ),
        };

        if ($coverImageUrl = $article->getCoverImage()) {
            $this->createPrestaImage($product, $coverImageUrl);
        }

        return $product;
    }

    /**
     * @param int $productId
     * @param ArticleStock $availability
     *
     * @return \Product
     *
     * @throws \PrestaShopException
     */
    private function updatePrestaProductFromArticle(int $productId, ArticleStock $availability): \Product
    {
        $product = new \Product($productId);
        $product->wholesale_price = $availability->prices->wholesalePrice;
        $product->price = $availability->prices->priceWithoutReductionsWithoutTax;
        $product->minimal_quantity = $availability->minimumOrderQuantity;
        $product->id_tax_rules_group = \Configuration::get(ConfigurationConstant::TECDOC_ID_TAX_RULES_GROUP);

        if ($availability->weight) {
            $product->weight = $availability->weight;
        }

        $product->save();

        return $product;
    }

    /**
     * @param \Product $product
     * @param ArticleStock $availability
     *
     * @return \Product
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function processPrestaProductRelations(\Product $product, ArticleStock $availability): \Product
    {
        if ($availability->prices->depositWithoutTax) {
            $this->createOrUpdateDepositProduct($product, $availability);
        } else {
            $this->deleteDepositProduct($product);
        }

        \SpecificPrice::deleteByProductId($product->id);

        foreach ($availability->prices->groupDiscounts as $articleStockGroupDiscount) {
            $this->createSpecificPriceForProduct($product, $articleStockGroupDiscount);
        }

        \StockAvailable::setQuantity($product->id, 0, $availability->stock, null, false);

        return $product;
    }

    /**
     * @param Article $article
     * @param int $tecdocSupplierId
     *
     * @return ArticleStock|bool
     */
    private function getAvailability(Article $article, int $tecdocSupplierId): ArticleStock|bool
    {
        $criteria = Criteria::create()
            ->where(Criteria::expr()->eq('tecdocSupplierId', $tecdocSupplierId));

        return $article
            ->availability
            ->matching($criteria)
            ->first();
    }

    /**
     * @param \Product $product
     * @param int $quantity
     *
     * @return void
     *
     * @throws GuzzleException
     * @throws MappingError
     * @throws NonUniqueResultException
     * @throws TecDocApiException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     * @throws CacheException
     */
    public function updateStockQuantityByProduct(\Product $product, int $quantity): void
    {
        if ($product->isTecDocModuleProduct()) {
            if ($product->isTecDocArticleProduct()) {
                $article = $this->articleService->getSingleArticle((int) $product->id_tecdoc_brand, $product->article_reference);
            } elseif ($product->isCustomArticleProduct()) {
                $article = $this->articleService->getSingleCustomArticle((int) $product->id_custom_article);
            }

            if (isset($article) and $article instanceof Article) {
                $this->stockService->updateQuantity($article, (int) $product->id_tecdoc_supplier, $quantity);
            }
        }
    }

    /**
     * @param \Product $product
     * @param ArticleStock $availability
     *
     * @return \Product
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function createOrUpdateDepositProduct(\Product $product, ArticleStock $availability): \Product
    {
        $depositProductName = Helper::getConfigInMultipleLanguages(ConfigurationConstant::TECDOC_DEPOSIT_PRODUCT_NAME);

        if ($depositProduct = $product->getDepositProduct()) {
            $depositProduct->name = $depositProductName;

            $depositProduct->link_rewrite = array_map(function ($v) {
                return \Tools::str2url($v);
            }, $depositProduct->name);

            $depositProduct->price = $availability->prices->depositWithoutTax;
            $depositProduct->id_tax_rules_group = \Configuration::get(ConfigurationConstant::TECDOC_ID_TAX_RULES_GROUP);

            $depositProduct->update();
        } else {
            $tecDocCategory = Helper::getTecDocCategory();

            $depositProduct = new \Product();
            $depositProduct->id_category_default = $tecDocCategory->id;
            $depositProduct->name = $depositProductName;

            $depositProduct->link_rewrite = array_map(function ($v) {
                return \Tools::str2url($v);
            }, $depositProduct->name);

            $depositProduct->reference = $product->reference;
            $depositProduct->price = $availability->prices->depositWithoutTax;

            $depositProduct->id_tax_rules_group = \Configuration::get(ConfigurationConstant::TECDOC_ID_TAX_RULES_GROUP);
            $depositProduct->visibility = 'none';
            $depositProduct->is_virtual = true;

            $depositProduct->add();
            $depositProduct->addToCategories($tecDocCategory->id);
            $depositProduct->markAsDepositProduct($product);
        }

        return $depositProduct;
    }

    /**
     * @param \Product $product
     *
     * @return bool
     *
     * @throws \PrestaShopException
     */
    private function deleteDepositProduct(\Product $product): bool
    {
        if ($depositProduct = $product->getDepositProduct()) {
            return $depositProduct->delete();
        }

        return false;
    }

    /**
     * @param \Product $product
     * @param string $imageUrl
     *
     * @return void
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function createPrestaImage(\Product $product, string $imageUrl): void
    {
        $position = \Image::getHighestPosition($product->id) + 1;

        $prestaImage = new \Image();
        $prestaImage->id_product = (int) $product->id;
        $prestaImage->position = $position;
        $prestaImage->cover = !($position > 1);

        if ($prestaImage->add()) {
            if (!self::copyImg((int) $prestaImage->id, $imageUrl)) {
                $prestaImage->delete();
            }
        }
    }

    /**
     * @param int $imageId
     * @param string $url
     *
     * @return bool
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function copyImg(int $imageId, string $url): bool
    {
        $tmpFile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');

        $url = str_replace(' ', '%20', trim($url));

        if (!\ImageManager::checkImageMemoryLimit($url)) {
            return false;
        }

        $opts = [
            'http' => [
                'method' => 'GET',
                'header' => "User-Agent: PrestaShop Connector ver.1.0\r\n",
            ],
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ];

        $context = stream_context_create($opts);
        if (@copy($url, $tmpFile, $context)) {
            $imageObj = new \Image($imageId);
            $path = $imageObj->getPathForCreation();

            \ImageManager::resize($tmpFile, $path . '.jpg');

            $imagesTypes = \ImageType::getImagesTypes('products');

            foreach ($imagesTypes as $imageType) {
                \ImageManager::resize($tmpFile, $path . '-' . stripslashes($imageType['name']) . '.jpg', $imageType['width'], $imageType['height']);
            }
        } else {
            unlink($tmpFile);

            return false;
        }

        unlink($tmpFile);

        return true;
    }

    /**
     * @param \Product $product
     * @param ArticleStockGroupDiscount $articleStockGroupDiscount
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    private function createSpecificPriceForProduct(\Product $product, ArticleStockGroupDiscount $articleStockGroupDiscount): void
    {
        $specificPrice = new \SpecificPrice();
        $specificPrice->id_product = $product->id;
        $specificPrice->id_shop = (int) \Context::getContext()->shop->id;
        $specificPrice->id_currency = 0;
        $specificPrice->id_country = 0;
        $specificPrice->id_group = $articleStockGroupDiscount->groupId;
        $specificPrice->id_customer = 0;
        $specificPrice->price = '-1';
        $specificPrice->from_quantity = 1;
        $specificPrice->reduction = $articleStockGroupDiscount->discountRate / 100;
        $specificPrice->reduction_type = 'percentage';
        $specificPrice->reduction_tax = 0;
        $specificPrice->from = '0000-00-00 00:00:00';
        $specificPrice->to = '0000-00-00 00:00:00';
        $specificPrice->add();
    }

    /**
     * @throws Exception
     * @throws \PrestaShopException
     * @throws \Doctrine\DBAL\Driver\Exception
     */
    public function deleteCachedProducts(): void
    {
        /**
         * Get cached TecDoc articles
         */
        $tecdocArticleProducts = $this
            ->connection
            ->createQueryBuilder()
            ->select('tp.id_product')
            ->from($this->dbPrefix . DatabaseConstant::TECDOC_PRODUCT_TABLE, 'tp')
            ->execute()
            ->fetchFirstColumn();

        /**
         * Get cached custom articles
         */
        $customArticleProducts = $this
            ->connection
            ->createQueryBuilder()
            ->select('tcp.id_product')
            ->from($this->dbPrefix . DatabaseConstant::TECDOC_CUSTOM_PRODUCT, 'tcp')
            ->execute()
            ->fetchFirstColumn();

        $productIds = array_merge($tecdocArticleProducts, $customArticleProducts);

        /**
         * Verify if cache products are in carts that haven't been ordered
         */
        $qb = $this->connection->createQueryBuilder();

        $productsInCarts = $qb
            ->select('cp.`id_product`')
            ->from($this->dbPrefix . 'cart_product', 'cp')
            ->innerJoin('cp', $this->dbPrefix . 'cart', 'c', 'cp.`id_cart` = c.`id_cart`')
            ->leftJoin('c', $this->dbPrefix . 'orders', 'o', 'c.`id_cart` = o.`id_cart`')
            ->where($qb->expr()->in('cp.`id_product`', ':productIds'))
            ->andWhere('o.`id_order` IS NULL')
            ->groupBy('cp.`id_product`')
            ->setParameter('productIds', $productIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchFirstColumn();

        /**
         * We are only deleting products that are currently unused
         */
        $productIdsForDelete = array_diff($productIds, $productsInCarts);

        $this->deleteProducts($productIdsForDelete);
    }

    /**
     * @param array $productIds
     *
     * @return void
     *
     * @throws \PrestaShopException
     */
    private function deleteProducts(array $productIds): void
    {
        foreach ($productIds as $productId) {
            $product = new \Product($productId);

            if (\Validate::isLoadedObject($product)) {
                $product->delete();
            }
        }
    }

    /**
     * @param \Product $product
     *
     * @return string|bool
     *
     * @throws CacheException
     * @throws GuzzleException
     * @throws MappingError
     * @throws TecDocApiException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function getProductLink(\Product $product): string|bool
    {
        if ($product->isTecDocArticleProduct()) {
            $article = $this
                ->articleService
                ->getSingleArticle((int) $product->id_tecdoc_brand, $product->article_reference);
        } elseif ($product->isCustomArticleProduct()) {
            $article = $this
                ->articleService
                ->getSingleCustomArticle((int) $product->id_custom_article);
        }

        return (isset($article) and $article instanceof Article)
            ? $article->getLink()
            : false;
    }
}
