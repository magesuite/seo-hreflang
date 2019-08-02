<?php
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$store = $objectManager->create('Magento\Store\Model\Store');
if (!$store->load('second', 'code')->getId()) {
    $store->setData(
        [
            'code' => 'second',
            'website_id' => 1,
            'group_id' => 1,
            'name' => 'second store',
            'sort_order' => 0,
            'is_active' => 1
        ]
    );
    $store->save();
}

$page = $objectManager->create(\Magento\Cms\Model\Page::class);
$page->setTitle('Page in default store')
    ->setIdentifier('page_in_default_store')
    ->setStores([1])
    ->setIsActive(1)
    ->setContent('<h1>Cms Page Design Blank Title</h1>')
    ->setMetaDescription('Meta description')
    ->setPageLayout('1column')
    ->setPageGroupIdentifier('group')
    ->save();

$page = $objectManager->create(\Magento\Cms\Model\Page::class);
$page->setTitle('Page in second store')
    ->setIdentifier('page_in_second_store')
    ->setStores([$store->getId()])
    ->setIsActive(1)
    ->setContent('<h1>Cms Page Design Blank Title</h1>')
    ->setMetaDescription('Meta description')
    ->setPageLayout('1column')
    ->setPageGroupIdentifier('group')
    ->save();

$page = $objectManager->create(\Magento\Cms\Model\Page::class);
$page->setTitle('Disabled page')
    ->setIdentifier('disabled_page')
    ->setStores([1])
    ->setIsActive(0)
    ->setContent('<h1>Cms Page Design Blank Title</h1>')
    ->setMetaDescription('Meta description')
    ->setPageLayout('1column')
    ->save();