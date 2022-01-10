<?php
declare(strict_types=1);

namespace MageSuite\SeoHreflang\Model\Entity;

class CmsPage implements EntityInterface
{
    const ALL_STORES_ID = 0;

    /**
     * @var \Magento\Cms\Api\Data\PageInterface
     */
    protected $page;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Page\CollectionFactory
     */
    protected $pageCollectionFactory;

    /**
     * @var \MageSuite\SeoHreflang\Helper\Configuration
     */
    protected $configuration;

    public function __construct(
        \Magento\Cms\Api\Data\PageInterface $page,
        \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory,
        \MageSuite\SeoHreflang\Helper\Configuration $configuration
    ) {
        $this->pageCollectionFactory = $pageCollectionFactory;
        $this->page = $page;
        $this->configuration = $configuration;
    }

    public function isApplicable(): bool
    {
        return (bool)$this->page->getId();
    }

    public function isActive(\Magento\Store\Api\Data\StoreInterface $store): bool
    {
        $page = $this->getPage($store);

        if (empty($page)) {
            return false;
        }

        return (bool)$page->getIsActive();
    }

    public function getUrl(\Magento\Store\Api\Data\StoreInterface $store): string
    {
        $page = $this->getPage($store);

        if (empty($page)) {
            return '';
        }

        $url = $store->getBaseUrl();
        $homePageIdentifier = $this->configuration->getHomepageIdentifier($store->getId());
        $homePageDelimiterPosition = strrpos($homePageIdentifier, '|');

        if ($homePageDelimiterPosition) {
            $homePageIdentifier = substr($homePageIdentifier, 0, $homePageDelimiterPosition);
        }

        if ($homePageIdentifier != $page->getIdentifier()) {
            $url .= $page->getIdentifier();
        }

        return $url;
    }

    protected function getPage(\Magento\Store\Api\Data\StoreInterface $store): ?\Magento\Cms\Model\Page
    {
        if (in_array($store->getId(), $this->page->getStoreId())) {
            return $this->page;
        }

        if (in_array(self::ALL_STORES_ID, $this->page->getStoreId())) {
            return $this->page;
        }

        if (empty($this->page->getPageGroupIdentifier())) {
            return null;
        }

        $page = $this->getCmsPage($store);

        if ($page->getId()) {
            return $page;
        }

        return null;
    }

    protected function getCmsPage(\Magento\Store\Api\Data\StoreInterface $store)
    {
        $collection = $this->pageCollectionFactory->create();
        $collection
            ->addFieldToFilter('page_group_identifier', $this->page->getPageGroupIdentifier())
            ->addFieldToFilter('store_id', $store->getId())
            ->addFieldToSelect(['identifier', 'page_id', 'is_active'])
            ->setPageSize(1);

        return $collection->getFirstItem();
    }
}
