<?php

declare(strict_types=1);

namespace MageSuite\SeoHreflang\Helper;

class Configuration
{
    public const XML_PATH_CONFIGURATION_KEY = 'seo/hreflang';
    public const XML_PATH_SEO_HREFLANG_HREFLANG_TAGS_ENABLED = 'seo/hreflang/hreflang_tags_enabled';
    public const XML_PATH_SEO_HREFLANG_HREFLANG_SCOPE = 'seo/hreflang/hreflang_scope';
    public const XML_PATH_SEO_HREFLANG_HREFLANG_DISABLE_ON_NON_CANONICAL = 'seo/hreflang/hreflang_disable_on_non_canonical';
    public const XML_PATH_SEO_HREFLANG_X_DEFAULT = 'seo/hreflang/x_default';
    public const XML_PATH_SEO_HREFLANG_HIDE_FOR_NOINDEX = 'seo/hreflang/hide_for_noindex';
    public const XML_PATH_SEO_HREFLANG_STRIP_PARAMETERS_FROM_THE_URL = 'seo/hreflang/strip_parameters_from_the_url';
    public const XML_PATH_SEO_HREFLANG_EXCLUDE_STORE = 'seo/hreflang/exclude_store';
    public const XML_PATH_SEO_HREFLANG_REMOVE_TRAILING_SLASH = 'seo/hreflang/remove_trailing_slash';

    public function __construct(
        protected \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
    ) {}

    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SEO_HREFLANG_HREFLANG_TAGS_ENABLED);
    }

    public function getHreflangScope(): string
    {
        return (string)$this->scopeConfig->getValue(self::XML_PATH_SEO_HREFLANG_HREFLANG_SCOPE);
    }

    public function isHreflangDisabledOnNonCanonicalPages(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SEO_HREFLANG_HREFLANG_DISABLE_ON_NON_CANONICAL);
    }

    public function getXDefaultStoreId(): int
    {
        return (int)$this->scopeConfig->getValue(self::XML_PATH_SEO_HREFLANG_X_DEFAULT, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getHomepageIdentifier(?int $storeId = null): string
    {
        return (string)$this->scopeConfig->getValue(\Magento\Cms\Helper\Page::XML_PATH_HOME_PAGE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function hideForNoindex(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SEO_HREFLANG_HIDE_FOR_NOINDEX, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function shouldStripParametersFromUrl(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SEO_HREFLANG_STRIP_PARAMETERS_FROM_THE_URL, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isStoreExcluded(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SEO_HREFLANG_EXCLUDE_STORE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }

    public function isRemovingTrailingSlashEnabled(?int $storeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SEO_HREFLANG_REMOVE_TRAILING_SLASH, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
    }
}
