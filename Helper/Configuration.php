<?php

namespace MageSuite\SeoHreflang\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_CONFIGURATION_KEY = 'seo/configuration';

    protected $config = null;

    public function isEnabled()
    {
        return $this->getConfig()->getHreflangTagsEnabled();
    }

    public function getXDefaultStoreId()
    {
        return $this->getConfig()->getXDefault();
    }

    public function getHreflangScope()
    {
        return $this->getConfig()->getHreflangScope();
    }

    protected function getConfig()
    {
        if ($this->config === null) {
            $config = $this->scopeConfig->getValue(self::XML_PATH_CONFIGURATION_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->config = new \Magento\Framework\DataObject($config);
        }

        return $this->config;
    }

    public function getHomepageIdentifier($storeId = null)
    {
        return $this->scopeConfig->getValue(
            \Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }
}
