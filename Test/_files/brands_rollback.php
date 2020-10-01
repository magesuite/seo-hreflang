<?php
if (interface_exists(\MageSuite\BrandManagement\Api\BrandsRepositoryInterface::class)) {
    /** @var \Magento\Framework\Registry $registry */
    $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

    $registry = $objectManager->get('Magento\Framework\Registry');

    $registry->unregister('isSecureArea');
    $registry->register('isSecureArea', true);
    $brandRepository = $objectManager->create('MageSuite\BrandManagement\Api\BrandsRepositoryInterface');
    foreach ([1989, 1991] as $brandId) {
        $brand = $objectManager->create('MageSuite\BrandManagement\Model\Brands');

        $brand->load($brandId);

        if ($brand->getId() > 0) {
            $brand->delete();
        }
    }
}
