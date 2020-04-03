<?php

namespace Fooman\EssentialCatalog\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Fooman\PhpunitBridge\BaseUnitTestCase;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\Data\StockStatusInterface;

/**
 * @magentoAppArea frontend
 */
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
            $this->stockRegistry->getStockStatus(2)->getStockStatus()
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
            $this->stockRegistry->getStockStatus(2)->getStockStatus()
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
            $this->stockRegistry->getStockStatus(1)->getStockStatus()
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_without_options_with_stock_data.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 0
     */
    public function testDefaultProductsAreSellableWhenDisabled()
    {
        $this->assertEquals(
            StockStatusInterface::STATUS_IN_STOCK,
            $this->stockRegistry->getStockStatus(1)->getStockStatus()
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_without_options_with_stock_data.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 1
     */
    public function testDefaultProductsWhenEnabled()
    {
        $this->assertEquals(
            StockStatusInterface::STATUS_IN_STOCK,
            $this->stockRegistry->getStockStatus(1)->getStockStatus()
        );

        $productRegistry = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);

        $product = $productRegistry->getById(1);
        $product->setFoomanIsProductEssential(false);
        $productRegistry->save($product);

        $this->assertEquals(
            StockStatusInterface::STATUS_OUT_OF_STOCK,
            $this->stockRegistry->getStockStatus(1)->getStockStatus()
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
