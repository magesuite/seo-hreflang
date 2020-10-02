<?php
if (interface_exists('MageSuite\BrandManagement\Api\BrandsRepositoryInterface')) {
    $brandRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('MageSuite\BrandManagement\Api\BrandsRepositoryInterface');

    $brand = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('MageSuite\BrandManagement\Model\Brands');
    $brand
        ->setEntityId(1989)
        ->setStoreId(0)
        ->setUrlKey('enabled-brand')
        ->setLayoutUpdateXml('layout update xml')
        ->setBrandName('test_brand_name')
        ->setEnabled(1)
        ->setIsFeatured(1)
        ->setBrandIcon('testimage.png')
        ->setBrandAdditionalIcon('testimage_additional.png')
        ->setMetaTitle('Test meta title')
        ->setMetaDescription('Test meta description')
        ->setMetaRobots('NOINDEX,NOFOLLOW');
    $brandRepository->save($brand);

    $brand = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('MageSuite\BrandManagement\Model\Brands');
    $brand
        ->setEntityId(1991)
        ->setStoreId(0)
        ->setUrlKey('disabled-brand')
        ->setLayoutUpdateXml('layout update xml')
        ->setBrandName('test_brand_name')
        ->setEnabled(0)
        ->setIsFeatured(1)
        ->setBrandIcon('testimage.png')
        ->setBrandAdditionalIcon('testimage_additional.png')
        ->setMetaTitle('Test meta title 2')
        ->setMetaDescription('Test meta description 2')
        ->setMetaRobots('NOINDEX,NOFOLLOW');
    $brandRepository->save($brand);
}
