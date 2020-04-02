<?php

namespace Fooman\EssentialCatalog\Model;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\TestFramework\Helper\Bootstrap;
use Fooman\PhpunitBridge\BaseUnitTestCase;

/**
 * @magentoAppArea frontend
 */
class ProductChangesStockStatusTest extends BaseUnitTestCase
{
    private $productRepository;

    protected function setUp()
    {
        parent::setUp();
        $this->productRepository = Bootstrap::getObjectManager()->get(ProductRepositoryInterface::class);
    }

    /**
     * @magentoDataFixture Fooman/EssentialCatalog/_files/non_essential_product.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 0
     */
    public function testNonEssentialProductsAreSellableWhenDisabled()
    {
        $product = $this->productRepository->getById(2);
        $this->assertTrue($product->isSalable());
    }

    /**
     * @magentoDataFixture Fooman/EssentialCatalog/_files/non_essential_product.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 1
     */
    public function testNonEssentialProductsAreNotSellableWhenEnabled()
    {
        $product = $this->productRepository->getById(2);
        $this->assertFalse($product->isSalable());
    }

    /**
     * @magentoDataFixture Fooman/EssentialCatalog/_files/essential_product.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 1
     */
    public function testEssentialProductsAreStillSellableWhenEnabled()
    {
        $product = $this->productRepository->getById(1);
        $this->assertTrue($product->isSalable());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_without_options.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 0
     */
    public function testDefaultProductsAreSellableWhenDisabled()
    {
        $product = $this->productRepository->getById(2);
        $this->assertTrue($product->isSalable());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_without_options.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 1
     */
    public function testDefaultProductsAreNotSellableWhenEnabled()
    {
        $product = $this->productRepository->getById(2);
        $this->assertFalse($product->isSalable());
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_virtual.php
     * @magentoConfigFixture current_store cataloginventory/item_options/fooman_enable_essential 1
     */
    public function testVirtualProductsAreSellableWhenEnabled()
    {
        $product = $this->productRepository->getById(21);
        $this->assertTrue($product->isSalable());
    }
}
