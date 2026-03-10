<?php

declare(strict_types=1);

namespace MageSuite\SeoHreflang\Test\Integration\Model\Entity;

class ProductTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager;
    protected ?\Magento\Framework\Registry $registry;
    protected ?\Magento\Store\Model\Store $store;
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository;
    protected ?\MageSuite\SeoHreflang\Model\Entity\Product $productEntity;
    protected ?\Magento\Framework\App\Request\Http $request;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->registry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $this->store = $this->objectManager->create(\Magento\Store\Model\Store::class);
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
        $this->productEntity = $this->objectManager->get(\MageSuite\SeoHreflang\Model\Entity\Product::class);
        $this->request = $this->objectManager->get(\Magento\Framework\App\Request\Http::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_SeoHreflang::Test/Integration/_files/products.php
     */
    public function testItReturnsCorrectData(): void
    {
        $activeProduct = $this->productRepository->get('active_product');
        $disabledProduct = $this->productRepository->get('disabled_product');

        $this->store->setId(1);

        $this->assertFalse($this->productEntity->isApplicable());

        $this->registry->register('product', $activeProduct);
        $this->assertTrue($this->productEntity->isApplicable());
        $this->assertTrue($this->productEntity->isActive($this->store));

        $this->registry->unregister('product');

        $this->registry->register('product', $disabledProduct);
        $this->assertFalse($this->productEntity->isActive($this->store));

        $this->registry->unregister('product');
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_SeoHreflang::Test/Integration/_files/product_url_rewrite.php
     */
    public function testItReturnsCorrectUrl(): void
    {
        $this->request->setPathInfo('catalog/product/view/id/100');

        $this->store->setId(1);
        $urlForFirstStore = $this->productEntity->getUrl($this->store);
        $this->assertEquals('http://localhost/index.php/active-product.html', $urlForFirstStore);

        $this->store->setId(2);
        $urlForSecondStore = $this->productEntity->getUrl($this->store);
        $this->assertEquals('http://localhost/index.php/rewrite_for_active_product.html', $urlForSecondStore);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_SeoHreflang::Test/Integration/_files/product_url_rewrite.php
     */
    public function testItThrowsExceptionWhenStoreIsNotSet(): void
    {
        try {
            $this->store->setId(3);
            $this->productEntity->getUrl($this->store);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals('The store that was requested wasn\'t found. Verify the store and try again.', $e->getMessage());
        }
    }
}
