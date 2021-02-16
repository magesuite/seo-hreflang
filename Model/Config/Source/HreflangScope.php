<?php

namespace MageSuite\SeoHreflang\Model\Config\Source;

class HreflangScope implements \Magento\Framework\Option\ArrayInterface
{
    const GLOBAL = 'global';
    const STOREGROUP = 'storegroup';

    public function toOptionArray()
    {
        return [self::GLOBAL => __('Global'), self::STOREGROUP => __('Store Group')];
    }
}