<?php
declare(strict_types=1);

namespace MageSuite\SeoHreflang\Model\Entity;

class Category implements EntityInterface
{
    /**
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \MageSuite\SeoHreflang\Model\ResourceModel\MultiStoreAttributeLoader
     */
    protected $multistoreAttributeLoader;

    public function __construct(
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $registry,
        \MageSuite\SeoHreflang\Model\ResourceModel\MultiStoreAttributeLoader $multistoreAttributeLoader
    ) {
        $this->urlFinder = $urlFinder;
        $this->request = $request;
        $this->registry = $registry;
        $this->multistoreAttributeLoader = $multistoreAttributeLoader;
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

        $isActive = $this->multistoreAttributeLoader->getAttributeRawValue(
            $category,
            'is_active',
            (int)$store->getId()
        );

        return (bool)$isActive;
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

        return $store->getCurrentUrl(false);
    }

    public function getCategory(): ?\Magento\Catalog\Model\Category
    {
        return $this->registry->registry('current_category');
    }
}
