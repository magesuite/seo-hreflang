<?php
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$category = $objectManager->create('Magento\Catalog\Model\Category');
$category->isObjectNew(true);
$category
    ->setId(333)
    ->setCreatedAt('2014-06-23 09:50:07')
    ->setName('Active Category')
    ->setParentId(2)
    ->setPath('1/2/333')
    ->setLevel(3)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(true)
    ->setPosition(1)
    ->setAvailableSortBy(['position'])
    ->setMetaDescription('Meta description')
    ->save()
    ->reindex();

$category = $objectManager->create('Magento\Catalog\Model\Category');
$category->isObjectNew(true);
$category
    ->setId(334)
    ->setCreatedAt('2014-06-23 09:50:07')
    ->setName('Disabled Category')
    ->setParentId(2)
    ->setPath('1/2/333')
    ->setLevel(3)
    ->setAvailableSortBy('name')
    ->setDefaultSortBy('name')
    ->setIsActive(false)
    ->setPosition(1)
    ->setAvailableSortBy(['position'])
    ->setMetaDescription('Meta description')
    ->save()
    ->reindex();