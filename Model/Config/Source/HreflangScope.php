<?php
declare(strict_types=1);

namespace MageSuite\SeoHreflang\Model\Config\Source;

class HreflangScope implements \Magento\Framework\Data\OptionSourceInterface
{
    const GLOBAL = 'global';
    const STOREGROUP = 'storegroup';

    public function toOptionArray(): array
    {
        return [
            self::GLOBAL => __('Global'),
            self::STOREGROUP => __('Store Group')
        ];
    }
}
