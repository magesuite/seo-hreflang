<?php

namespace MageSuite\SeoHreflang\Block;

class Hreflang extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'MageSuite_SeoHreflang::hreflang.phtml';

    const SEO_HREFLANG_TAGS_PATH = 'seo/configuration/hreflang_tags_enabled';
    const SEO_X_DEFAULT_PATH = 'seo/configuration/x_default';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;
    /**
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    private $urlFinder;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Magento\Framework\App\RequestInterface $request,
        array $data = []
    )
    {
        parent::__construct($context, $data);

        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->urlFinder = $urlFinder;
        $this->request = $request;
    }

    public function getStoresData()
    {
        $storesData = [];

        $stores = $this->storeManager->getStores();
        foreach ($stores as $store) {
            $hreflangCode = $store->getHreflangCode() ? $store->getHreflangCode() : str_replace('_', '-', $store->getCode());
            $url = $this->getCurrentUrl($store);
            $storesData[] = [
                'url' => $url,
                /**
                 * It is required that language and region codes are separated by dash "-"
                 * instead of underscore "_" which Magento returns.
                 */
                'code' => $hreflangCode
            ];
        }

        return $storesData;
    }

    public function isEnabled()
    {
        return $this->scopeConfig->getValue(
            self::SEO_HREFLANG_TAGS_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
    }

    public function getXDefaultUrl()
    {
        $xDefaultStoreId = $this->scopeConfig->getValue(
            self::SEO_X_DEFAULT_PATH,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE
        );
        if ($xDefaultStoreId < 0) {
            return null;
        }

        $store = $this->storeManager->getStore($xDefaultStoreId);

        return $this->getCurrentUrl($store);
    }

    private function getCurrentUrl($store){
        $url = $store->getCurrentUrl(false);

        $queryValue = $this->request->getQueryValue();
        $urlRewrite = $this->urlFinder->findOneByData([
            'target_path' => trim($this->request->getPathInfo(), '/'),
            'store_id' => $store->getId()
        ]);
        if ($urlRewrite) {
            $query = $queryValue ? '?' . http_build_query($queryValue, '', '&amp;') : '';
            $url = $this->urlBuilder->getUrl($store->getBaseUrl() . $urlRewrite->getRequestPath() . $query);
            $url = trim($url, '/');
        }

        return $url;
    }

}
