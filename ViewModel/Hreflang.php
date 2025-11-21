<?php

declare(strict_types=1);

namespace MageSuite\SeoHreflang\ViewModel;

class Hreflang implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    public const X_DEFAULT = 'x-default';
    public const QUERY_SEPARATOR = '&amp;';

    protected \Magento\Store\Model\StoreManagerInterface $storeManager;
    protected \Magento\Framework\UrlInterface $urlBuilder;
    protected \Magento\Framework\App\RequestInterface $request;
    protected \Magento\Framework\View\Page\Config $pageConfig;
    protected \MageSuite\SeoHreflang\Helper\Configuration $configuration;
    protected \MageSuite\SeoHreflang\Model\EntityPool $entityPool;
    protected \MageSuite\SeoCanonical\Helper\Configuration $canonicalConfiguration;

    protected array $allowedQueryParameters = [];

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\View\Page\Config $pageConfig,
        \MageSuite\SeoHreflang\Helper\Configuration $configuration,
        \MageSuite\SeoHreflang\Model\EntityPool $entityPool,
        \MageSuite\SeoCanonical\Helper\Configuration $canonicalConfiguration,
        array $allowedQueryParameters = []
    ) {
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->pageConfig = $pageConfig;
        $this->configuration = $configuration;
        $this->entityPool = $entityPool;
        $this->canonicalConfiguration = $canonicalConfiguration;
        $this->allowedQueryParameters = $allowedQueryParameters;
    }

    public function getAlternateLinks(): array
    {
        /** @var \MageSuite\SeoHreflang\Model\Entity\EntityInterface $entity */
        $entity = $this->entityPool->getEntity();

        if (empty($entity)) {
            return [];
        }

        $stores = $this->getStores();
        $alternateLinks = [];

        foreach ($stores as $store) {
            if (!$this->isApplicable($entity, $store)) {
                continue;
            }

            $alternateLink = $this->getAlternateLink($entity, $store);

            if (empty($alternateLink)) {
                continue;
            }

            $alternateLinks[$store->getId()] = $alternateLink;
        }

        $this->addXDefaultUrl($alternateLinks);

        return $alternateLinks;
    }

    public function isApplicable(\MageSuite\SeoHreflang\Model\Entity\EntityInterface $entity, \Magento\Store\Api\Data\StoreInterface $store): bool
    {
        if (!$store->getIsActive()) {
            return false;
        }

        if ($this->configuration->isStoreExcluded((int)$store->getId())) {
            return false;
        }

        if (!$entity->isActive($store)) {
            return false;
        }

        if (!$this->configuration->isEnabled()) {
            return false;
        }

        $robots = $this->pageConfig->getRobots();
        if ($this->configuration->hideForNoindex((int)$store->getId()) && $robots && str_contains($robots, 'NOINDEX')) {
            return false;
        }

        return true;
    }

    protected function getStores(): array
    {
        if ($this->configuration->getHreflangScope() === \MageSuite\SeoHreflang\Model\Config\Source\HreflangScope::GLOBAL) {
            return $this->storeManager->getStores();
        }

        return $this->storeManager->getGroup()->getStores();
    }

    protected function getAlternateLink(\MageSuite\SeoHreflang\Model\Entity\EntityInterface $entity, \Magento\Store\Model\Store $store): ?\Magento\Framework\DataObject
    {
        $url = $entity->getUrl($store);

        if (empty($url)) {
            return null;
        }

        $url = $this->addQueryToUrl($url);

        $alternateLink = [
            'url' => $url,
            'code' => $this->getHreflangCode($store)
        ];

        return new \Magento\Framework\DataObject($alternateLink);
    }

    /**
     * It is required that language and region codes are separated by dash "-"
     * instead of underscore "_" which Magento returns.
     */
    protected function getHreflangCode(\Magento\Store\Model\Store $store): string
    {
        return $store->getHreflangCode() ?? str_replace('_', '-', $store->getCode());
    }

    protected function addXDefaultUrl(&$alternateLinks): void //phpcs:ignore
    {
        $xDefaultStoreId = $this->configuration->getXDefaultStoreId();

        if (!$xDefaultStoreId || !isset($alternateLinks[$xDefaultStoreId])) {
            return;
        }

        $xDefaultLink = clone $alternateLinks[$xDefaultStoreId];
        $xDefaultLink->setCode(self::X_DEFAULT);
        $alternateLinks[self::X_DEFAULT] = $xDefaultLink;
    }

    public function addQueryToUrl(string $url): string
    {
        $queryValue = $this->request->getQueryValue();

        if (!$this->configuration->shouldStripParametersFromUrl()) {
            return $this->buildUrlWithQuery($url, $queryValue);
        }

        if (!$this->canonicalConfiguration->isCanonicalForPaginatedPagesEnabled() || !$this->canonicalConfiguration->isCanonicalPageParamEnabled()) {
            return $url;
        }

        $queryValue = array_intersect_key($queryValue, array_flip($this->allowedQueryParameters));

        return $this->buildUrlWithQuery($url, $queryValue);
    }

    protected function buildUrlWithQuery(string $url, array $queryValue): string
    {
        $query = http_build_query($queryValue, '', self::QUERY_SEPARATOR);
        $splitUrl = \Laminas\Uri\UriFactory::factory($url);
        $rawUrl = $splitUrl->getScheme() . '://' . $splitUrl->getHost() . $splitUrl->getPath();

        if (empty($query)) {
            return trim($rawUrl, '/');
        }
        
        $urlWithQuery = sprintf('%s?%s', $rawUrl, $query);
        $url = $this->urlBuilder->getUrl($urlWithQuery);

        return trim($url, '/');
    }
}
