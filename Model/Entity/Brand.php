<?php
namespace MageSuite\SeoHreflang\Model\Entity;

class Brand implements EntityInterface
{
    const BRANDS_FULL_ACTION_NAME = 'brands_index_all';
    const BRAND_REGISTRY_KEY = 'current_brand';

    /**
     * @var \Magento\Store\Model\App\Emulation
     */
    protected $emulation;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\Store\Model\App\Emulation $emulation,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $registry
    ) {
        $this->emulation = $emulation;
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
        $url = $store->getCurrentUrl(false);
        $brand = $this->registry->registry(self::BRAND_REGISTRY_KEY);
        if ($brand === null) {
            return $url;
        }
        $this->emulation->startEnvironmentEmulation($store->getStoreId(), \Magento\Framework\App\Area::AREA_FRONTEND, true);
        $url = $brand->getBrandUrl();
        $this->emulation->stopEnvironmentEmulation();
        return $url;
    }

    protected function isBrandIndexPage()
    {
        return $this->request->getFullActionName() === self::BRANDS_FULL_ACTION_NAME;
    }
}
