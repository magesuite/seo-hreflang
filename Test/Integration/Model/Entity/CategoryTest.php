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

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->registry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $this->store = $this->objectManager->create(\Magento\Store\Model\Store::class);
        $this->categoryRepository = $this->objectManager->get(\Magento\Catalog\Api\CategoryRepositoryInterface::class);

        $this->categoryEntity = $this->objectManager->get(\MageSuite\SeoHreflang\Model\Entity\Category::class);
        $this->request = $this->objectManager->get(\Magento\Framework\App\Request\Http::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_SeoHreflang::Test/Integration/_files/categories.php
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

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_SeoHreflang::Test/Integration/_files/category_url_rewrite.php
     */
    public function testItReturnsCorrectUrl()
    {
        $this->request->setPathInfo('catalog/category/view/id/100');

        $this->store->setId(1);
        $urlForFirstStore = $this->categoryEntity->getUrl($this->store);
        $this->assertEquals('http://localhost/index.php/active-category.html', $urlForFirstStore);

        $this->store->setId(2);
        $urlForSecondStore = $this->categoryEntity->getUrl($this->store);
        $this->assertEquals('http://localhost/index.php/rewrite_for_active_category.html', $urlForSecondStore);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_SeoHreflang::Test/Integration/_files/category_url_rewrite.php
     */
    public function testItThrowsExceptionWhenStoreIsNotSet()
    {
        try {
            $this->store->setId(3);
            $this->categoryEntity->getUrl($this->store);
            $this->fail();
        } catch (\Exception $e) {
            $this->assertEquals('The store that was requested wasn\'t found. Verify the store and try again.', $e->getMessage());
        }
    }
}
