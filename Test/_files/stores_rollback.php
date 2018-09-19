<?php
/** @var $store \Magento\Store\Model\Store */
$store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Store');

$storeCodes = [
    'test_hreflang_store_1',
    'test_hreflang_store_2'
];
/** @var $storeResource Magento\Store\Model\ResourceModel\Store */
$storeResource = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\ResourceModel\Store');

foreach ($storeCodes as $code) {
    $store = $store->load($code, 'code');

    if ($store->getId()) {
        $storeResource->delete($store);
    }
}

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$objectManager->get('Magento\Store\Model\StoreManagerInterface')->reinitStores();
