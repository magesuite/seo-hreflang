<?php
namespace MageSuite\SeoHreflang\Test\Integration\Model\Entity;

class CategoryTest extends \PHPUnit\Framework\TestCase
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
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var \MageSuite\SeoHreflang\Model\Entity\Category
     */
    protected $categoryEntity;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->registry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $this->store = $this->objectManager->create(\Magento\Store\Model\Store::class);
        $this->categoryRepository = $this->objectManager->get(\Magento\Catalog\Api\CategoryRepositoryInterface::class);

        $this->categoryEntity = $this->objectManager->get(\MageSuite\SeoHreflang\Model\Entity\Category::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture categoriesFixture
     */
    public function testItReturnsCorrectData()
    {
        $activeCategory = $this->categoryRepository->get(333);
        $disabledCategory = $this->categoryRepository->get(334);

        $this->assertFalse($this->categoryEntity->isApplicable());

        $this->registry->register('current_category', $activeCategory);
        $this->assertTrue($this->categoryEntity->isApplicable());
        $this->assertTrue($this->categoryEntity->isActive($this->store));

        $this->registry->unregister('current_category');

        $this->registry->register('current_category', $disabledCategory);
        $this->assertFalse($this->categoryEntity->isActive($this->store));

        $this->registry->unregister('current_category');
    }

    public static function categoriesFixture()
    {
        include __DIR__ . '/../../../_files/categories.php';
    }

    public static function categoriesFixtureRollback()
    {
        include __DIR__ . '/../../../_files/categories_rollback.php';
    }
}
