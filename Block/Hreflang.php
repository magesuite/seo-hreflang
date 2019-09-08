<?php

namespace MageSuite\SeoHreflang\Block;

class Hreflang extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'MageSuite_SeoHreflang::hreflang.phtml';

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
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \MageSuite\SeoHreflang\Helper\Configuration $configuration,
        \MageSuite\SeoHreflang\Model\EntityPool $entityPool,
        array $data = []
    ){
        parent::__construct($context, $data);

        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->configuration = $configuration;
        $this->entityPool = $entityPool;
    }

    public function getAlternateLinks()
    {
        $alternateLinks = [];

        /** @var \MageSuite\SeoHreflang\Model\Entity\EntityInterface $entity */
        $entity = $this->entityPool->getEntity();

        if(empty($entity)){
            return $alternateLinks;
        }

        $stores = $this->storeManager->getStores();

        foreach ($stores as $store) {

            if(!$entity->isActive($store)){
                continue;
            }

            $alternateLink = $this->getAlternateLink($entity, $store);

            if(empty($alternateLink)){
                continue;
            }

            $alternateLinks[$store->getId()] = $alternateLink;
        }

        $this->addXDefaultUrl($alternateLinks);

        return $alternateLinks;
    }

    public function isEnabled()
    {
        return $this->configuration->isEnabled();
    }

    protected function getAlternateLink(\MageSuite\SeoHreflang\Model\Entity\EntityInterface $entity, $store)
    {
        $url = $entity->getUrl($store);

        if(empty($url)){
            return null;
        }

        $this->addQueryToUrl($url);

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
    protected function getHreflangCode($store)
    {
        return $store->getHreflangCode() ? $store->getHreflangCode() : str_replace('_', '-', $store->getCode());
    }

    protected function addXDefaultUrl(&$alternateLinks)
    {
        $xDefaultStoreId = $this->configuration->getXDefaultStoreId();

        if ($xDefaultStoreId < 0) {
            return;
        }

        if(!isset($alternateLinks[$xDefaultStoreId])){
            return;
        }

        $xDefaultLink = clone $alternateLinks[$xDefaultStoreId];
        $xDefaultLink->setCode(self::X_DEFAULT);

        $alternateLinks[self::X_DEFAULT] = $xDefaultLink;
    }

    protected function addQueryToUrl(&$url)
    {
        $queryValue = $this->request->getQueryValue();

        if(empty($queryValue)){
            return;
        }

        $query = http_build_query($queryValue, '', self::QUERY_SEPARATOR);

        $splitedUrl = parse_url($url);

        $rawUrl = $splitedUrl['scheme'] . '://' . $splitedUrl['host'] . $splitedUrl['path'];

        $urlWithQuery = sprintf('%s?%s', $rawUrl, $query);

        $url = $this->urlBuilder->getUrl($urlWithQuery);
        $url = trim($url, '/');
    }
}
