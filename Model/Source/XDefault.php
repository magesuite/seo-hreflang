<?php

namespace MageSuite\SeoHreflang\Model\Source;

class XDefault implements \Magento\Framework\Option\ArrayInterface
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager
    )
    {
        $this->storeManager = $storeManager;
    }

    public function toOptionArray()
    {
        $options = [['label' => __('None'), 'value' => '-1']];

        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $websiteName = $store->getWebsite()->getName();
            $storeName = $store->getName();
            $storeCode = $store->getCode();
            $storeId = $store->getStoreId();

            $value = $websiteName . " | " . $storeName . " (code: " . $storeCode . " | ID: " . $storeId . ")";

            $options[] = ['label' => $value, 'value' => $storeId];
        }

        return $options;
    }
}