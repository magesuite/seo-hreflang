<?php
namespace MageSuite\SeoHreflang\Test\Integration\Block;

class HreflangTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var \Magento\TestFramework\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Magento\Store\Model\Store
     */
    protected $store;

    /**
     * @var \MageSuite\SeoHreflang\Block\Hreflang
     */
    protected $hreflangBlock;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();

        $this->store = $this->objectManager->create(\Magento\Store\Model\Store::class);

        $this->hreflangBlock = $this->objectManager->create(\MageSuite\SeoHreflang\Block\Hreflang::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadStores
     */
    public function testItReturnsCorrectHreflangCodeIfSet()
    {
        $hreflangBlockResult = $this->hreflangBlock->getStoresData();

        $this->assertEquals('store_1', $hreflangBlockResult[1]['code']);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture loadStores
     */
    public function testItReturnsDefaultHreflangIfCodeIsNotSet()
    {
        $hreflangBlockResult = $this->hreflangBlock->getStoresData();

        $this->assertEquals('test-hreflang-store-2', $hreflangBlockResult[2]['code']);
    }

    public static function loadStores() {
        include __DIR__.'/../../_files/stores.php';
    }

    public static function loadStoresRollback() {
        include __DIR__.'/../../_files/stores_rollback.php';
    }
}