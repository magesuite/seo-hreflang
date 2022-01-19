<?php
declare(strict_types=1);

namespace MageSuite\SeoHreflang\ViewModel;

class Hreflang implements \Magento\Framework\View\Element\Block\ArgumentInterface
{
    const X_DEFAULT = 'x-default';
    const QUERY_SEPARATOR = '&amp;';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \MageSuite\SeoHreflang\Helper\Configuration
     */
    protected $configuration;

    /**
     * @var \MageSuite\SeoHreflang\Model\EntityPool
     */
    protected $entityPool;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \MageSuite\SeoHreflang\Helper\Configuration $configuration,
        \MageSuite\SeoHreflang\Model\EntityPool $entityPool
    ) {
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->configuration = $configuration;
        $this->entityPool = $entityPool;
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

    protected function isApplicable(
        \MageSuite\SeoHreflang\Model\Entity\EntityInterface $entity,
        \Magento\Store\Api\Data\StoreInterface $store
    ): bool {
        return $store->getIsActive()
            && !$this->configuration->isStoreExcluded($store)
            && $entity->isActive($store);
    }

    protected function getStores(): array
    {
        if ($this->configuration->getHreflangScope() === \MageSuite\SeoHreflang\Model\Config\Source\HreflangScope::GLOBAL) {
            return $this->storeManager->getStores();
        }

        return $this->storeManager->getGroup()->getStores();
    }

    protected function getAlternateLink(
        \MageSuite\SeoHreflang\Model\Entity\EntityInterface $entity,
        \Magento\Store\Model\Store $store
    ): ?\Magento\Framework\DataObject {
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
     *
     * @param \Magento\Store\Model\Store $store
     * @return string
     */
    protected function getHreflangCode(\Magento\Store\Model\Store $store): string
    {
        return $store->getHreflangCode() ?? str_replace('_', '-', $store->getCode());
    }

    protected function addXDefaultUrl(&$alternateLinks): void
    {
        $xDefaultStoreId = $this->configuration->getXDefaultStoreId();

        if (!$xDefaultStoreId || !isset($alternateLinks[$xDefaultStoreId])) {
            return;
        }

        $xDefaultLink = clone $alternateLinks[$xDefaultStoreId];
        $xDefaultLink->setCode(self::X_DEFAULT);
        $alternateLinks[self::X_DEFAULT] = $xDefaultLink;
    }

    protected function addQueryToUrl($url): string
    {
        $queryValue = $this->request->getQueryValue();

        if (empty($queryValue)) {
            return $url;
        }

        $query = http_build_query($queryValue, '', self::QUERY_SEPARATOR);
        $splitUrl = \Zend_Uri_Http::fromString($url);
        $rawUrl = $splitUrl->getScheme() . '://' . $splitUrl->getHost() . $splitUrl->getPath();
        $urlWithQuery = sprintf('%s?%s', $rawUrl, $query);
        $url = $this->urlBuilder->getUrl($urlWithQuery);

        return trim($url, '/');
    }
}
