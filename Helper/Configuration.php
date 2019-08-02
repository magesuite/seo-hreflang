<?php

namespace MageSuite\SeoHreflang\Helper;

class Configuration extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_CONFIGURATION_KEY = 'seo/configuration';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    protected $config = null;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfigInterface
    ) {
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

    protected function getConfig()
    {
        if($this->config === null){
            $config = $this->scopeConfig->getValue(self::XML_PATH_CONFIGURATION_KEY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
            $this->config = new \Magento\Framework\DataObject($config);
        }

        return $this->config;
    }
}
