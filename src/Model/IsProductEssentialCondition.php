<?php

namespace Fooman\EssentialCatalog\Model;

use Magento\Catalog\Model\Product\Attribute\Source\Boolean;
use Magento\InventoryCatalogApi\Model\GetProductIdsBySkusInterface;
use Magento\InventorySalesApi\Api\IsProductSalableInterface;

class IsProductEssentialCondition implements IsProductSalableInterface
{
    const ESSENTIAL_PRODUCT_ATTR = 'fooman_is_product_essential';

    const XML_PATH_ESSENTIAL_ONLY_ENABLED = 'cataloginventory/item_options/fooman_enable_essential';

    /**
     * @var array
     */
    private $cachedResultBySku = [];

    /**
     * @var GetProductIdsBySkusInterface
     */
    private $getProductIdsBySkus;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    private $productResource;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    private $storeManager;

    public function __construct(
        GetProductIdsBySkusInterface $getProductIdsBySkus,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->getProductIdsBySkus = $getProductIdsBySkus;
        $this->productResource = $productResource;
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function execute(string $sku, int $stockId): bool
    {
        if (!$this->scopeConfig->isSetFlag(
            self::XML_PATH_ESSENTIAL_ONLY_ENABLED,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        )) {
            return true;
        }
        if (!isset($this->cachedResultBySku[$sku])) {
            $productId = $this->getProductIdsBySkus->execute([$sku])[$sku];
            $isEssential = $this->productResource->getAttributeRawValue(
                $productId,
                self::ESSENTIAL_PRODUCT_ATTR,
                $this->storeManager->getStore()
            );
            $this->cachedResultBySku[$sku] = $this->determineResult($isEssential);
        }
        return $this->cachedResultBySku[$sku];
    }

    public function unsetCachedResult($sku)
    {
        if (isset($this->cachedResultBySku[$sku])) {
            unset($this->cachedResultBySku[$sku]);
        }
    }

    private function determineResult($input)
    {
        if (is_array($input)) {
            return true;
        }
        if ($input == Boolean::VALUE_USE_CONFIG) {
            return true;
        }
        return (bool)$input;
    }
}
