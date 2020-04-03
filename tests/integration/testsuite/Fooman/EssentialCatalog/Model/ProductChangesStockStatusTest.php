<?php

namespace Fooman\EssentialCatalog\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\CatalogInventory\Model\StockRegistryStorage;
use Magento\TestFramework\Helper\Bootstrap;
use Fooman\PhpunitBridge\BaseUnitTestCase;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\Data\StockStatusInterface;
use Magento\Catalog\Model\Product;

class ProductChangesStockStatusTest extends BaseUnitTestCase
{

    private $stockRegistry;

    protected function setUp()
    {
        parent::setUp();
        $this->stockRegistry = Bootstrap::getObjectManager()->get(StockRegistryInterface::class);
    }

    /**
     * @magentoDataFixture Fooman/EssentialCatalog/_files/non_essential_product.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 0
     */
    public function testNonEssentialProductsAreSellableWhenDisabled()
    {
        $this->assertEquals(
            StockStatusInterface::STATUS_IN_STOCK,
            $this->stockRegistry->getStockStatus(155)->getStockStatus()
        );
    }

    /**
     * @magentoDataFixture Fooman/EssentialCatalog/_files/non_essential_product.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 1
     */
    public function testNonEssentialProductsAreNotSellableWhenEnabled()
    {
        $this->assertEquals(
            StockStatusInterface::STATUS_OUT_OF_STOCK,
            $this->stockRegistry->getStockStatus(155)->getStockStatus()
        );
    }

    /**
     * @magentoDataFixture Fooman/EssentialCatalog/_files/essential_product.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 1
     */
    public function testEssentialProductsAreStillSellableWhenEnabled()
    {
        $this->assertEquals(
            StockStatusInterface::STATUS_IN_STOCK,
            $this->stockRegistry->getStockStatus(154)->getStockStatus()
        );
    }

    /**
     * @magentoDataFixture Fooman/EssentialCatalog/_files/default_product.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 0
     */
    public function testDefaultProductsAreSellableWhenDisabled()
    {
        $this->assertEquals(
            StockStatusInterface::STATUS_IN_STOCK,
            $this->stockRegistry->getStockStatus(156)->getStockStatus()
        );
    }

    /**
     * @magentoDataFixture Fooman/EssentialCatalog/_files/default_product.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 1
     */
    public function testDefaultProductsWhenEnabled()
    {
        $this->assertEquals(
            StockStatusInterface::STATUS_IN_STOCK,
            $this->stockRegistry->getStockStatus(156)->getStockStatus()
        );

        $productRegistry = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
        $stockRegistryStorage = Bootstrap::getObjectManager()->get(StockRegistryStorage::class);
        $product = $productRegistry->getById(156);
        $product->setFoomanIsProductEssential(0);
        $productRegistry->save($product);
        $stockRegistryStorage->removeStockStatus(156);

        $condition = Bootstrap::getObjectManager()->get(IsProductEssentialCondition::class);
        $condition->unsetCachedResult($product->getSku());

        $this->assertEquals(
            StockStatusInterface::STATUS_OUT_OF_STOCK,
            $this->stockRegistry->getStockStatus(156)->getStockStatus()
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_virtual.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 0
     */
    public function testVirtualProductsAreSellableWhenDisabled()
    {
        $this->assertEquals(
            StockStatusInterface::STATUS_IN_STOCK,
            $this->stockRegistry->getStockStatus(21)->getStockStatus()
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_virtual.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 1
     */
    public function testVirtualProductsAreSellableWhenEnabled()
    {
        $this->assertEquals(
            StockStatusInterface::STATUS_IN_STOCK,
            $this->stockRegistry->getStockStatus(21)->getStockStatus()
        );
    }
}
