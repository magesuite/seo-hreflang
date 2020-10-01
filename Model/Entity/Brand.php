<?php
namespace MageSuite\SeoHreflang\Model\Entity;

class Brand implements EntityInterface
{
    const BRANDS_FULL_ACTION_NAME = 'brands_index_all';
    const BRAND_REGISTRY_KEY = 'current_brand';

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

    public function __construct(
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $registry
    ) {
        $this->urlFinder = $urlFinder;
        $this->request = $request;
        $this->registry = $registry;
    }

    public function isApplicable()
    {
        if ($this->isBrandIndexPage()) {
            return true;
        }
        return $this->registry->registry(self::BRAND_REGISTRY_KEY) ? true : false;
    }

    public function isActive($store)
    {
        if ($this->isBrandIndexPage()) {
            return true;
        }
        $brand = $this->registry->registry(self::BRAND_REGISTRY_KEY);
        if ($brand === null) {
            return false;
        }
        $brandResource = $brand->getResource();
        $isEnabled = $brandResource->getAttributeRawValue($brand->getEntityId(), 'enabled', $store->getId());
        if ($isEnabled === false) {
            $isEnabled = $brandResource->getAttributeRawValue($brand->getEntityId(), 'enabled', \Magento\Store\Model\Store::DEFAULT_STORE_ID);
        }
        return boolval($isEnabled);
    }

    public function getUrl($store)
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

    protected function isBrandIndexPage()
    {
        return $this->request->getFullActionName() === self::BRANDS_FULL_ACTION_NAME;
    }
}
