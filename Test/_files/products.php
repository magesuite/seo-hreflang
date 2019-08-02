<?php
$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

$product = $objectManager->create(\Magento\Catalog\Model\Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(555)
    ->setAttributeSetId(4)
    ->setName('Active Product')
    ->setSku('active_product')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED)
    ->setWebsiteIds([1])
    ->setStoreIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->setDescription('<p>Description</p>')
    ->save();

$product->reindex();
$product->priceReindexCallback();

$product = $objectManager->create(\Magento\Catalog\Model\Product::class);
$product->setTypeId(\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE)
    ->setId(556)
    ->setAttributeSetId(4)
    ->setName('Disabled Product')
    ->setSku('disabled_product')
    ->setPrice(10)
    ->setVisibility(\Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH)
    ->setStatus(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_DISABLED)
    ->setWebsiteIds([1])
    ->setStoreIds([1])
    ->setStockData(['use_config_manage_stock' => 1, 'qty' => 100, 'is_qty_decimal' => 0, 'is_in_stock' => 1])
    ->setCanSaveCustomOptions(true)
    ->setDescription('<p>Description</p>')
    ->save();

$product->reindex();
$product->priceReindexCallback();