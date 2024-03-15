<?php
declare(strict_types=1);

namespace MageSuite\SeoHreflang\Model\Entity;

class Product implements EntityInterface
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
        return (bool)$this->getProduct();
    }

    public function isActive(\Magento\Store\Api\Data\StoreInterface $store): bool
    {
        $product = $this->getProduct();

        if (!in_array($store->getId(), $product->getStoreIds())) {
            return false;
        }

        $status = $this->multistoreAttributeLoader->getAttributeRawValue(
            $product,
            'status',
            (int)$store->getId()
        );

        return $status == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED;
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

    public function getProduct(): ?\Magento\Catalog\Model\Product
    {
        return $this->registry->registry('product');
    }
}
