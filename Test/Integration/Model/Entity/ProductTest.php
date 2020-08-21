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

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->registry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $this->store = $this->objectManager->create(\Magento\Store\Model\Store::class);
        $this->productRepository = $this->objectManager->get(\Magento\Catalog\Api\ProductRepositoryInterface::class);

        $this->productEntity = $this->objectManager->get(\MageSuite\SeoHreflang\Model\Entity\Product::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture productsFixture
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

    public static function productsFixture()
    {
        include __DIR__ . '/../../../_files/products.php';
    }

    public static function productsFixtureRollback()
    {
        include __DIR__ . '/../../../_files/products_rollback.php';
    }
}
