<?php

declare(strict_types=1);

namespace MageSuite\SeoHreflang\Test\Integration\ViewModel;

class HreflangTest extends \Magento\TestFramework\TestCase\AbstractController
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager;
    protected ?\Magento\Catalog\Api\ProductRepositoryInterface $productRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Store/_files/second_store.php
     * @magentoConfigFixture seo/configuration/canonical_tag_enabled 1
     * @magentoConfigFixture default/seo/hreflang/hreflang_tags_enabled 1
     * @magentoConfigFixture default/seo/hreflang/hreflang_disable_on_non_canonical 0
     */
    public function testProductPageContainsHreflangTags(): void
    {
        $product = $this->productRepository->get('simple');

        $url = '/catalog/product/view/id/' . $product->getId() . '/?test_param=1';
        $this->dispatch($url);
        $response = $this->getResponse();
        $html = $response->getBody();

        $this->assertEquals(200, $response->getHttpResponseCode());

        $this->assertStringContainsString(
            'rel="alternate"',
            $html,
            'Hreflang rel="alternate" tag not found in product page HTML.'
        );
    }

    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation disabled
     * @magentoAppIsolation enabled
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     * @magentoDataFixture Magento/Store/_files/second_store.php
     * @magentoConfigFixture seo/configuration/canonical_tag_enabled 1
     * @magentoConfigFixture default/seo/hreflang/hreflang_tags_enabled 1
     * @magentoConfigFixture default/seo/hreflang/hreflang_disable_on_non_canonical 1
     */
    public function testProductPageNotContainsHreflangTags(): void
    {
        $product = $this->productRepository->get('simple');

        $url = '/catalog/product/view/id/' . $product->getId() . '/?test_param=1';
        $this->dispatch($url);
        $response = $this->getResponse();
        $html = $response->getBody();

        $this->assertEquals(200, $response->getHttpResponseCode());

        $this->assertStringNotContainsString(
            'rel="alternate"',
            $html,
            'Hreflang rel="alternate" tag found in product page HTML.'
        );
    }
}
