<?php
declare(strict_types=1);

namespace MageSuite\SeoHreflang\Model\Entity;

interface EntityInterface
{
    /**
     * @return bool
     */
    public function isApplicable(): bool;

    /**
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return bool
     */
    public function isActive(\Magento\Store\Api\Data\StoreInterface $store): bool;

    /**
     * @param \Magento\Store\Api\Data\StoreInterface $store
     * @return string
     */
    public function getUrl(\Magento\Store\Api\Data\StoreInterface $store): string;
}
