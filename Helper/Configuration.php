<?php

namespace MageSuite\SeoHreflang\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_CONFIGURATION_KEY = 'seo/configuration';
    const DEFAULT_HOMEPAGE_ID = 'web/default/cms_home_page';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $config = null;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    )
    {
        parent::__construct($context);

        $this->scopeConfig = $scopeConfigInterface;
    }

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

    public function shouldTranslateHreflangTags()
    {
        return $this->getConfig()->getTranslateHreflangTags();
    }

    protected function getConfig()
    {
        if ($this->config === null) {
            $config = $this->scopeConfig->getValue(self::XML_PATH_CONFIGURATION_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->config = new \Magento\Framework\DataObject($config);
        }

        return $this->config;
    }

    public function getDefaultHomepageId()
    {
        return $this->scopeConfig->getValue(self::DEFAULT_HOMEPAGE_ID, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
