<?php

namespace Xigen\OrderHistory\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

/**
 * Order history config mapping helper class
 */
class Config extends AbstractHelper
{
    const STORE_ID_MAPPING = 'orderhistory/mapping/store';
    const STATUS_MAPPING = 'orderhistory/mapping/status';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Get magento 2 store ID to magento 1 store ID
     * @return void
     */
    public function getStoreIdMappings()
    {
        $rawMappings = $this->scopeConfig->getValue(
            self::STORE_ID_MAPPING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );

        $rawMappings = json_decode($rawMappings);
        $mappings = [];
        foreach ($rawMappings as $rawMapping) {
            $storeId = $rawMapping->store_id;
            $storeIdMapping = $rawMapping->store_id_mapping;
            $mappings[$storeIdMapping] = $storeId;
        }

        return $mappings;
    }

    /**
     * Get magento status code to status label mapping
     * @return array
     */
    public function getStatusMapping()
    {
        $rawMappings = $this->scopeConfig->getValue(
            self::STATUS_MAPPING,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()->getId()
        );

        $rawMappings = json_decode($rawMappings);
        $mappings = [];
        foreach ($rawMappings as $rawMapping) {
            $mappings[$rawMapping->status_id] = $rawMapping->status_id_mapping;
        }

        return $mappings;
    }
}
