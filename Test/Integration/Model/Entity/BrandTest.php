<?php
namespace MageSuite\SeoHreflang\Test\Integration\Model\Entity;

class BrandTest extends \PHPUnit\Framework\TestCase
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
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \MageSuite\BrandManagement\Api\BrandsRepositoryInterface
     */
    protected $brandRepository;

    /**
     * @var \MageSuite\SeoHreflang\Model\Entity\Brand
     */
    protected $brandEntity;

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
     * @magentoDataFixture brandsFixture
     * @magentoConfigFixture default_store web/url/use_store 1
     * @magentoConfigFixture second_store web/url/use_store 1
     */
    public function testItReturnsCorrectData()
    {
        $store = $this->storeManager->getStore('default');

        $activeBrand = $this->brandRepository->getById(1989);
        $disabledBrand = $this->brandRepository->getById(1991);

        $this->assertFalse($this->brandEntity->isApplicable());
        $this->registry->register('current_brand', $activeBrand);

        $this->assertTrue($this->brandEntity->isApplicable());
        $this->assertTrue($this->brandEntity->isActive($store));
        $this->assertEquals('http://localhost/index.php/default/brands/enabled-brand', $this->brandEntity->getUrl($store));

        $secondStore = $this->storeManager->getStore('second');

        $this->assertEquals('http://localhost/index.php/second/brands/enabled-brand', $this->brandEntity->getUrl($secondStore));

        $this->registry->unregister('current_brand');

        $this->registry->register('current_brand', $disabledBrand);
        $this->assertFalse($this->brandEntity->isActive($store));
        $this->registry->unregister('current_brand');
    }

    public static function brandsFixture()
    {
        include __DIR__ . '/../../../_files/brands.php';
    }

    public static function brandsFixtureRollback()
    {
        include __DIR__ . '/../../../_files/brands_rollback.php';
    }
}
