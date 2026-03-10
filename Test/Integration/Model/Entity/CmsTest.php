<?php

declare(strict_types=1);

namespace MageSuite\SeoHreflang\Test\Integration\Model\Entity;

class CmsTest extends \PHPUnit\Framework\TestCase
{
    protected ?\Magento\TestFramework\ObjectManager $objectManager;
    protected ?\Magento\Store\Model\Store $store;
    protected ?\Magento\Cms\Api\PageRepositoryInterface $pageRepository;

    public function setUp(): void
    {
        $this->objectManager = \Magento\TestFramework\ObjectManager::getInstance();
        $this->store = $this->objectManager->create(\Magento\Store\Model\Store::class);
        $this->pageRepository = $this->objectManager->get(\Magento\Cms\Api\PageRepositoryInterface::class);
    }

    /**
     * @magentoDbIsolation enabled
     * @magentoDataFixture MageSuite_SeoHreflang::Test/Integration/_files/pages.php
     */
    public function testItReturnsCorrectData(): void
    {
        $cmsPageEntity = $this->objectManager->create(\MageSuite\SeoHreflang\Model\Entity\CmsPage::class);
        $this->assertFalse($cmsPageEntity->isApplicable());

        $page = $this->pageRepository->getById('page_in_default_store');
        $cmsPageEntity = $this->objectManager->create(
            \MageSuite\SeoHreflang\Model\Entity\CmsPage::class,
            ['page' => $page]
        );

        $this->assertTrue($cmsPageEntity->isApplicable());

        $this->store->setId(1);
        $this->assertTrue($cmsPageEntity->isActive($this->store));
        $this->assertEquals('http://localhost/index.php/page_in_default_store', $cmsPageEntity->getUrl($this->store));

        $secondStoreId = $this->store->load('second', 'code')->getId();
        $this->store->setId($secondStoreId);

        $this->assertTrue($cmsPageEntity->isActive($this->store));
        $this->assertEquals('http://localhost/index.php/page_in_second_store', $cmsPageEntity->getUrl($this->store));

        $page = $this->pageRepository->getById('disabled_page');
        $cmsPageEntity = $this->objectManager->create(
            \MageSuite\SeoHreflang\Model\Entity\CmsPage::class,
            ['page' => $page]
        );

        $this->store->setId(1);
        $this->assertFalse($cmsPageEntity->isActive($this->store));
    }
}
