<?php

declare(strict_types=1);

namespace MageSuite\SeoHreflang\Model\Entity;

class Product implements EntityInterface
{
    public function __construct(
        protected \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        protected \Magento\Framework\App\RequestInterface $request,
        protected \Magento\Framework\Registry $registry,
        protected \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
    }

    public function isApplicable(): bool
    {
        return (bool)$this->getProduct();
    }

    public function isActive(\Magento\Store\Api\Data\StoreInterface $store): bool
    {
        $product = $this->getProduct();

        if (!in_array($store->getId(), $product->getStoreIds())) {
            return false;
        }

        $status = $product->getResource()->getAttributeRawValue(
            $product->getId(),
            'status',
            $store
        );

        return $status == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
    }

    public function getUrl(\Magento\Store\Api\Data\StoreInterface $store): string
    {
        $targetPath = $this->prepareTargetPath();

        $urlRewrite = $this->urlFinder->findOneByData([
            'target_path' => $targetPath,
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

    public function getProduct(): ?\Magento\Catalog\Model\Product
    {
        return $this->registry->registry('product');
    }

    protected function prepareTargetPath(): string
    {
        $targetPath = trim($this->request->getPathInfo(), '/');

        return preg_replace('/\/category\/\d+/', '', $targetPath);
    }
}
