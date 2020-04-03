<?php

namespace Fooman\EssentialCatalog\Controller\Adminhtml;

/**
 * @magentoAppArea adminhtml
 */
class SystemConfigPageTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    public function setUp()
    {
        $this->resource = 'Magento_CatalogInventory::cataloginventory';
        $this->uri = 'backend/admin/system_config/edit/section/cataloginventory/';
        parent::setUp();
    }

    public function testAclNoAccess()
    {
        $this->_objectManager->get(\Magento\Framework\Acl\Builder::class)
            ->getAcl()
            ->deny(null, $this->resource);
        $this->dispatch($this->uri);

        //denied access in the system config redirects
        $this->assertTrue($this->getResponse()->isRedirect());
    }

    public function testSettingIsShowing()
    {
        $this->dispatch($this->uri);
        $this->assertContains('Restrict Catalog to Essential Products', $this->getResponse()->getBody());
    }
}

