<?php

declare(strict_types=1);

namespace MageSuite\SeoHreflang\Model\Entity;

class Category implements EntityInterface
{
    protected \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder;
    protected \Magento\Framework\App\RequestInterface $request;
    protected \Magento\Framework\Registry $registry;
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;

    public function __construct(
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $registry,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->urlFinder = $urlFinder;
        $this->request = $request;
        $this->registry = $registry;
        $this->storeManager = $storeManager;
    }

    public function isApplicable(): bool
    {
        $product = $this->registry->registry('product');
        $category = $this->getCategory();

        return !$product && $category;
    }

    public function isActive(\Magento\Store\Api\Data\StoreInterface $store): bool
    {
        $category = $this->getCategory();

        return (bool)$category->getResource()->getAttributeRawValue(
            $category->getId(),
            'is_active',
            $store
        );
    }

    public function getUrl(\Magento\Store\Api\Data\StoreInterface $store): string
    {
        $urlRewrite = $this->urlFinder->findOneByData([
            'target_path' => trim($this->request->getPathInfo(), '/'),
            'store_id' => $store->getId()
        ]);

        if ($urlRewrite) {
            return $store->getBaseUrl() . $urlRewrite->getRequestPath();
        }

        if ($this->isCurrentStore($store)) {
            return $store->getCurrentUrl(false);
        }

        return '';
    }

    protected function isCurrentStore(\Magento\Store\Api\Data\StoreInterface $store): bool
    {
        return (int)$store->getId() === (int)$this->storeManager->getStore()->getId();
    }

    public function getCategory(): ?\Magento\Catalog\Model\Category
    {
        return $this->registry->registry('current_category');
    }
}
