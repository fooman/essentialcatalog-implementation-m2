<?php

namespace Fooman\EssentialCatalog\Controller\Adminhtml;

class AttributeIsShowingOnProductTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_without_options.php
     * @magentoAppArea adminhtml
     */
    public function testIsEssentialDropdownIsShowing()
    {
        $this->dispatch('backend/catalog/product/edit/id/1/');
        $this->assertEquals(200, $this->getResponse()->getStatusCode());
        $this->assertContains('Essential Product', $this->getResponse()->getBody());
    }
}