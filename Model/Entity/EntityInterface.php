<?php

declare(strict_types=1);

namespace MageSuite\SeoHreflang\Model\Entity;

interface EntityInterface
{
    public function isApplicable(): bool;

    public function isActive(\Magento\Store\Api\Data\StoreInterface $store): bool;

    public function getUrl(\Magento\Store\Api\Data\StoreInterface $store): string;
}
