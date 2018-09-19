<?php
/** @var $store \Magento\Store\Model\Store */
$store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Store');
if (!$store->load('test_hreflang_store_1', 'code')->getId()) {
    $store->setData(
        [
            'code' => 'test_hreflang_store_1',
            'website_id' => '1',
            'group_id' => '1',
            'name' => 'test hreflang store 1',
            'sort_order' => '0',
            'is_active' => '1',
            'hreflang_code' => 'store_1'
        ]
    );
    $store->save();
}
$store = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Store\Model\Store');
if (!$store->load('test_hreflang_store_2', 'code')->getId()) {
    $store->setData(
        [
            'code' => 'test_hreflang_store_2',
            'website_id' => '1',
            'group_id' => '1',
            'name' => 'test hreflang store 2',
            'sort_order' => '0',
            'is_active' => '1',
            'hreflang_code' => ''
        ]
    );
    $store->save();
}
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
/* Refresh stores memory cache */
$objectManager->get('Magento\Store\Model\StoreManagerInterface')->reinitStores();
