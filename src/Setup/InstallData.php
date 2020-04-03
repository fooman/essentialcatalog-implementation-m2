<?php

namespace Fooman\EssentialCatalog\Setup;

use Fooman\EssentialCatalog\Model\IsProductEssentialCondition;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Eav\Setup\EavSetupFactory;

class InstallData implements InstallDataInterface
{
    private $eavSetupFactory;

    public function __construct(EavSetupFactory $eavSetupFactory)
    {
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->addEssentialFieldToProduct($setup);
    }

    private function addEssentialFieldToProduct(ModuleDataSetupInterface $installer)
    {
        /** @var \Magento\Eav\Setup\EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $installer]);

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            IsProductEssentialCondition::ESSENTIAL_PRODUCT_ATTR,
            [
                'group' => 'General',
                'type' => 'int',
                'input' => 'select',
                'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                'label' => 'Essential Product',
                'class' => '',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'comment' => '',
                'visible' => true,
                'required' => false,
                'default' => null,
                'searchable' => false,
                'filterable' => true,
                'comparable' => false,
                'visible_on_front' => false,
                'unique' => false,
                'nullable'=> true,
                'used_in_product_listing' => true,
                'apply_to' => \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE,
            ]
        );
    }
}
