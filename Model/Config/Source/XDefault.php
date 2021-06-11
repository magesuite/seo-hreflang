<?php

namespace MageSuite\SeoHreflang\Model\Config\Source;

class XDefault implements \Magento\Framework\Option\ArrayInterface
{
    const LABEL_FORMAT = '%s | %s (code: %s | ID: %s)';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(\Magento\Store\Model\StoreManagerInterface $storeManager)
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
            $label =  sprintf(self::LABEL_FORMAT, $websiteName, $storeName, $storeCode, $storeId);

            $options[] = ['label' => $label, 'value' => $storeId];
        }

        return $options;
    }
}