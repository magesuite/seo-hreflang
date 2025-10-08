<?php

declare(strict_types=1);

namespace MageSuite\SeoHreflang\Plugin\Model;

class Store
{
    protected \Magento\Store\Model\StoreManagerInterface $storeManager;
    protected \Magento\Framework\Session\SidResolverInterface $sidResolver;
    protected \Magento\Framework\App\RequestInterface $request;
    protected \Magento\Framework\UrlInterface $url;
    protected \Magento\Framework\Session\SessionManagerInterface $session;
    protected \Magento\Framework\App\ProductMetadataInterface $productMetadata;
    protected \Laminas\Uri\Http $uri;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\App\ProductMetadataInterface $productMetadata,
        \Laminas\Uri\Http $uri
    ) {
        $this->storeManager = $storeManager;
        $this->sidResolver = $sidResolver;
        $this->request = $request;
        $this->url = $url;
        $this->session = $session;
        $this->productMetadata = $productMetadata;
        $this->uri = $uri;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function aroundGetCurrentUrl(\Magento\Store\Model\Store $subject, callable $proceed, $fromStore = true): string
    {
        if (version_compare($this->productMetadata->getVersion(), '2.3.5', '<')) {
            $sidQueryParam = $this->sidResolver->getSessionIdQueryParam($this->getSession($subject->getCode()));
        }

        $requestString = $this->url->escape(
            preg_replace(
                '/\?.*?$/',
                '',
                ltrim($this->request->getRequestString(), '/')
            )
        );
        $storeUrl = $subject->getUrl('', ['_secure' => $this->storeManager->getStore()->isCurrentlySecure()]);

        if (!filter_var($storeUrl, FILTER_VALIDATE_URL)) {
            return $storeUrl;
        }

        $storeParsedUrl = \Laminas\Uri\UriFactory::factory($storeUrl);
        $storeParsedQuery = [];

        if ($storeParsedUrl->getQuery()) {
            $storeParsedQuery = $this->uri
                ->setQuery($storeParsedUrl->getQuery())
                ->getQueryAsArray();
        }

        $currentQuery = $this->request->getQueryValue();

        if (
            isset($sidQueryParam)
            && !empty($currentQuery[$sidQueryParam])
            && $this->getSession($subject->getCode())->getSessionIdForHost($storeUrl) != $currentQuery[$sidQueryParam]
        ) {
            unset($currentQuery[$sidQueryParam]);
        }

        foreach ($currentQuery as $key => $value) {
            $storeParsedQuery[$key] = $value;
        }

        if (!$subject->isUseStoreInUrl()) {
            $storeParsedQuery['___store'] = $subject->getCode();
        }

        if ($fromStore !== false) {
            $storeParsedQuery['___from_store'] = $fromStore === true
                ? $this->storeManager->getStore()->getCode()
                : $fromStore;
        }

        $isDefaultPort = in_array(
            $storeParsedUrl->getPort(),
            [
                \Magento\Framework\App\Request\Http::DEFAULT_HTTP_PORT,
                \Magento\Framework\App\Request\Http::DEFAULT_HTTPS_PORT
            ]
        );

        return $storeParsedUrl->getScheme()
            . '://'
            . $storeParsedUrl->getHost()
            . (!$isDefaultPort ? ':' . $storeParsedUrl->getPort() : '')
            . $storeParsedUrl->getPath()
            . $requestString
            . ($storeParsedQuery ? '?' . http_build_query($storeParsedQuery) : '');
    }

    protected function getSession(string $code): \Magento\Framework\Session\SessionManagerInterface
    {
        if (!$this->session->isSessionExists()) {
            $this->session->setName('store_' . $code);
            $this->session->start();
        }

        return $this->session;
    }
}
