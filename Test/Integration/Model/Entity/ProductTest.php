<?php
namespace MageSuite\SeoHreflang\Test\Integration\Model\Entity;

class ProductTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \MageSuite\SeoHreflang\Model\Entity\Product
     */
    protected $productEntity;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

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
    public function testItReturnsCorrectData()
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
    public function testItReturnsCorrectUrl()
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
    public function testItThrowsExceptionWhenStoreIsNotSet()
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
