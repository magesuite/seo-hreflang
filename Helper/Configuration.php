<?php
declare(strict_types=1);

namespace MageSuite\SeoHreflang\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_CONFIGURATION_KEY = 'seo/configuration';

    protected $config;

    public function isEnabled(): bool
    {
        return (bool)$this->getConfig()->getHreflangTagsEnabled();
    }

    public function getXDefaultStoreId(): int
    {
        return (int)$this->getConfig()->getXDefault();
    }

    public function getHreflangScope(): string
    {
        return (string)$this->getConfig()->getHreflangScope();
    }

    public function getHomepageIdentifier($storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(
            \Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    protected function getConfig(): \Magento\Framework\DataObject
    {
        if ($this->config === null) {
            $config = $this->scopeConfig->getValue(
                self::XML_PATH_CONFIGURATION_KEY,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
            $this->config = new \Magento\Framework\DataObject($config);
        }

        return $this->config;
    }
}
