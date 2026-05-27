<?php

declare(strict_types=1);

namespace MageSuite\SeoHreflang\Test\Integration\Model\Entity;

class BrandTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager;
    protected ?\Magento\Framework\Registry $registry;
    protected ?\Magento\Store\Model\StoreManagerInterface $storeManager;
    protected ?\MageSuite\BrandManagement\Api\BrandsRepositoryInterface $brandRepository;
    protected ?\MageSuite\SeoHreflang\Model\Entity\Brand $brandEntity;

    public function setUp(): void
    {
        if (!interface_exists('MageSuite\BrandManagement\Api\BrandsRepositoryInterface')) {
            $this->markTestSkipped('Skipped because Brand Management module is not present');
        }

        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->registry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $this->storeManager = $this->objectManager->get(\Magento\Store\Model\StoreManagerInterface::class);
        $this->brandRepository = $this->objectManager->get(\MageSuite\BrandManagement\Api\BrandsRepositoryInterface::class);
        $this->brandEntity = $this->objectManager->get(\MageSuite\SeoHreflang\Model\Entity\Brand::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_SeoHreflang::Test/Integration/_files/brands.php
     * @magentoConfigFixture default_store web/url/use_store 1
     * @magentoConfigFixture second_store web/url/use_store 1
     */
    public function testItReturnsCorrectData(): void
    {
        $store = $this->storeManager->getStore('default');

        $activeBrand = $this->brandRepository->getById(1989);
        $disabledBrand = $this->brandRepository->getById(1991);

        $this->assertFalse($this->brandEntity->isApplicable());
        $this->registry->register('current_brand', $activeBrand);

        $this->assertTrue($this->brandEntity->isApplicable());
        $this->assertTrue($this->brandEntity->isActive($store));
        $this->assertEquals('http://localhost/index.php/brands/enabled-brand', $this->brandEntity->getUrl($store));

        $secondStore = $this->storeManager->getStore('second');

        $this->assertEquals('http://localhost/index.php/brands/enabled-brand', $this->brandEntity->getUrl($secondStore));

        $this->registry->unregister('current_brand');

        $this->registry->register('current_brand', $disabledBrand);
        $this->assertFalse($this->brandEntity->isActive($store));
        $this->registry->unregister('current_brand');
    }
}
