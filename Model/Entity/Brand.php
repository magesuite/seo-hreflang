<?php
declare(strict_types=1);

namespace MageSuite\SeoHreflang\Model\Entity;

class Brand implements EntityInterface
{
    const BRANDS_FULL_ACTION_NAME = 'brands_index_all';
    const BRAND_REGISTRY_KEY = 'current_brand';

    /**
     * @var \MageSuite\BrandManagement\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \MageSuite\BrandManagement\Helper\Configuration $configuration,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $registry
    ) {
        $this->configuration = $configuration;
        $this->request = $request;
        $this->registry = $registry;
    }

    public function isApplicable(): bool
    {
        if ($this->isBrandIndexPage()) {
            return true;
        }

        return (bool)$this->getBrand();
    }

    public function isActive(\Magento\Store\Api\Data\StoreInterface $store): bool
    {
        if ($this->isBrandIndexPage()) {
            return true;
        }

        $brand = $this->getBrand();

        if ($brand === null) {
            return false;
        }

        $brandResource = $brand->getResource();
        $isEnabled = $brandResource->getAttributeRawValue(
            $brand->getEntityId(),
            'enabled',
            $store->getId()
        );

        if ($isEnabled === false) {
            $isEnabled = $brandResource->getAttributeRawValue(
                $brand->getEntityId(),
                'enabled',
                \Magento\Store\Model\Store::DEFAULT_STORE_ID
            );
        }

        return (bool)$isEnabled;
    }

    public function getUrl(\Magento\Store\Api\Data\StoreInterface $store): string
    {
        $brand = $this->getBrand();

        if ($brand === null) {
            return $store->getBaseUrl() . $this->configuration->getRouteToBrand($store->getId());
        }

        return $brand->getBrandUrl($store);
    }

    protected function isBrandIndexPage(): bool
    {
        return $this->request->getFullActionName() === self::BRANDS_FULL_ACTION_NAME;
    }

    public function getBrand(): ?\MageSuite\BrandManagement\Model\Brands
    {
        return $this->registry->registry(self::BRAND_REGISTRY_KEY);
    }
}
