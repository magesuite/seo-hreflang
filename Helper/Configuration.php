<?php

declare(strict_types=1);

namespace MageSuite\SeoHreflang\Helper;

class Configuration
{
    public const XML_PATH_CONFIGURATION_KEY = 'seo/configuration';
    public const XML_PATH_SEO_CONFIGURATION_HREFLANG_TAGS_ENABLED = 'seo/configuration/hreflang_tags_enabled';
    public const XML_PATH_SEO_CONFIGURATION_HREFLANG_SCOPE = 'seo/configuration/hreflang_scope';
    public const XML_PATH_SEO_CONFIGURATION_X_DEFAULT = 'seo/configuration/x_default';
    public const XML_PATH_SEO_CONFIGURATION_HIDE_FOR_NOINDEX = 'seo/configuration/hide_for_noindex';
    public const XML_PATH_SEO_CONFIGURATION_STRIP_PARAMETERS_FROM_THE_URL = 'seo/configuration/strip_parameters_from_the_url';
    public const XML_PATH_SEO_CONFIGURATION_EXCLUDE_STORE = 'seo/configuration/exclude_store';

    protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SEO_CONFIGURATION_HREFLANG_TAGS_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE);
    }

    public function getHreflangScope(): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_SEO_CONFIGURATION_HREFLANG_SCOPE);
    }

    public function getXDefaultStoreId(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_SEO_CONFIGURATION_X_DEFAULT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getHomepageIdentifier(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(\Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function hideForNoindex(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SEO_CONFIGURATION_HIDE_FOR_NOINDEX, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function shouldStripParametersFromUrl(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SEO_CONFIGURATION_STRIP_PARAMETERS_FROM_THE_URL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isStoreExcluded(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SEO_CONFIGURATION_EXCLUDE_STORE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
}
