<?php

namespace MageSuite\SeoHreflang\Block;

class Hreflang extends \Magento\Framework\View\Element\Template
{
    protected $_template = 'MageSuite_SeoHreflang::hreflang.phtml';

    const X_DEFAULT = 'x-default';
    const QUERY_SEPARATOR = '&amp;';

    protected $mappedParameterTranslations = [];

    protected $separator = '';

    protected $stores = [];

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

    /**
     * @var \MageSuite\SeoHreflang\Model\ResourceModel\FiltrableAttributeOptionValues
     */
    protected $filtrableAttributeOptionValues;

    /**
     * @var \MageSuite\SeoLinkMasking\Helper\Configuration
     */
    protected $seoLinkConfiguration;

    /**
     * @var \MageSuite\SeoLinkMasking\Service\FiltrableAttributeUtfFriendlyConverter
     */
    protected $utfFriendlyConverter;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \MageSuite\SeoHreflang\Helper\Configuration $configuration,
        \MageSuite\SeoHreflang\Model\EntityPool $entityPool,
        \MageSuite\SeoHreflang\Model\ResourceModel\FiltrableAttributeOptionValues $filtrableAttributeOptionValues,
        \MageSuite\SeoLinkMasking\Helper\Configuration $seoLinkConfiguration,
        \MageSuite\SeoLinkMasking\Service\FiltrableAttributeUtfFriendlyConverter $utfFriendlyConverter,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->storeManager = $storeManager;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->configuration = $configuration;
        $this->entityPool = $entityPool;
        $this->filtrableAttributeOptionValues = $filtrableAttributeOptionValues;
        $this->seoLinkConfiguration = $seoLinkConfiguration;
        $this->utfFriendlyConverter = $utfFriendlyConverter;
    }

    public function getAlternateLinks()
    {
        $alternateLinks = [];

        /** @var \MageSuite\SeoHreflang\Model\Entity\EntityInterface $entity */
        $entity = $this->entityPool->getEntity();

        if (empty($entity)) {
            return $alternateLinks;
        }

        $this->stores = $this->getStores();
        foreach ($this->stores as $store) {
            if (!$entity->isActive($store)) {
                continue;
            }

            $alternateLink = $this->getAlternateLink($entity, $store);

            if (empty($alternateLink)) {
                continue;
            }

            $alternateLinks[$store->getId()] = $alternateLink;
        }

        $this->addXDefaultUrl($alternateLinks);

        if ($alternateLinks && $this->configuration->shouldTranslateHreflangTags()) {
            $this->separator = $this->seoLinkConfiguration->getMultiselectOptionSeparator();
            $alternateLinks = $this->processHreflangUrls($alternateLinks);
        }

        return $alternateLinks;
    }

    public function isEnabled()
    {
        return $this->configuration->isEnabled();
    }

    protected function getStores()
    {
        if ($this->configuration->getHreflangScope() === \MageSuite\SeoHreflang\Model\Config\Source\HreflangScope::GLOBAL) {
            $stores = $this->storeManager->getStores();
        } else {
            $stores = $this->storeManager->getGroup()->getStores();
        }

        $storesByCode = [];

        foreach ($stores as $store) {
            $storesByCode[$store->getCode()] = $store;
        }

        return $storesByCode;
    }

    protected function getAlternateLink(\MageSuite\SeoHreflang\Model\Entity\EntityInterface $entity, $store)
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

        if (!isset($alternateLinks[$xDefaultStoreId])) {
            return;
        }

        $xDefaultLink = clone $alternateLinks[$xDefaultStoreId];
        $xDefaultLink->setCode(self::X_DEFAULT);

        $alternateLinks[self::X_DEFAULT] = $xDefaultLink;
    }

    public function addQueryToUrl($url)
    {
        $queryValue = $this->request->getQueryValue();

        if (empty($queryValue)) {
            return $url;
        }

        $query = http_build_query($queryValue, '', self::QUERY_SEPARATOR);

        $splitedUrl = parse_url($url);

        $rawUrl = $splitedUrl['scheme'] . '://' . $splitedUrl['host'] . $splitedUrl['path'];

        $urlWithQuery = sprintf('%s?%s', $rawUrl, $query);

        $url = $this->urlBuilder->getUrl($urlWithQuery);
        $url = trim($url, '/');

        return $url;
    }

    protected function getParameterOptionValuesGrouped($parameters)
    {
        $translationsMapped = $this->getParameterOptionValuesFromDb($parameters);
        $groupedTranslations = [];

        foreach ($translationsMapped as $translation) {
            if (count($translation) > 1) {
                foreach ($translation as $storeId => $translated) {
                    $translatedIndex = strtolower($translated);
                    $groupedTranslations[$translatedIndex] = $translation;
                }
            }
        }

        return $groupedTranslations;
    }

    protected function getParameterOptionValuesFromDb($parameters)
    {
        return $this->filtrableAttributeOptionValues->getFiltrableOptionValues($parameters);
    }

    protected function processHreflangUrls($hrefLangs)
    {
        $parameters = $this->getUrlParameters($hrefLangs);
        $this->mappedParameterTranslations = $this->getParameterOptionValuesGrouped($parameters);

        foreach ($hrefLangs as $hrefLang) {
            $storeId = null;
            $url = urldecode($hrefLang->getUrl());
            $url = str_replace(' ', '+', $url);

            $urlParsed = parse_url($url);

            $parts = explode('/', $urlParsed['path']);
            $parts = $this->checkMultiOptionsParameters($parts);
            $newParts = [];

            foreach ($parts as $part) {
                if (empty($part)) {
                    $newParts[] = $part;
                    continue;
                }

                if (!is_array($part) && isset($this->stores[$part])) {
                    $currentStore = $this->stores[$part];
                    $storeId = $currentStore->getId();
                }

                if (is_array($part)) {
                    $multiOptionsParamTranslated = [];
                    foreach ($part as $p) {
                        $multiOptionsParamTranslated[] = $this->mapParamsTranslation(strtolower($p), $storeId);
                    }
                    $newParts[] = implode($this->separator, $multiOptionsParamTranslated);
                    continue;
                }

                $newParts[] = $this->mapParamsTranslation($part, $storeId);;
            }

            if ($this->seoLinkConfiguration->isUtfFriendlyModeEnabled()) {
                $newParts = $this->utfFriendlyConverter->convertFilterParams($newParts);
            }

            $newParts = $this->prepareUrlNewParts($newParts);
            $newPath = implode('/', $newParts);
            $urlParsed['path'] = $newPath;
            $newUrl = $urlParsed['scheme'] . '://' . $urlParsed['host'] . $urlParsed['path'];
            $hrefLang->setUrl($newUrl);
        }

        return $hrefLangs;
    }

    protected function prepareUrlNewParts($params)
    {
        foreach ($params as &$param) {
            $param = strtolower($param);
            $param = str_replace(' ', '+', $param);
        }

        return $params;
    }

    protected function convertSpaceCharacter($param)
    {
        return str_replace('+', ' ', $param);
    }

    protected function mapParamsTranslation($part, $storeId)
    {
        $part = $this->convertSpaceCharacter($part);
        if (!isset($this->mappedParameterTranslations[$part])) {
            return $part;
        }

        if (empty($storeId)) {
            $part = $this->mappedParameterTranslations[$part][\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        }

        if (isset($this->mappedParameterTranslations[$part][$storeId])) {
            $part = $this->mappedParameterTranslations[$part][$storeId];
        } else {
            $part = $this->mappedParameterTranslations[$part][\Magento\Store\Model\Store::DEFAULT_STORE_ID];
        }

        return $part;
    }

    protected function getUrlParameters($hrefLangs)
    {
        $parameters = [];
        foreach ($hrefLangs as $hrefLang) {
            $urlParts = parse_url(urldecode($hrefLang->getUrl()));
            $parameters = explode('/', $urlParts['path']);
            $parameters = $this->checkMultiOptionsParameters($parameters);
        }

        return $parameters;
    }

    protected function checkMultiOptionsParameters($parameters)
    {
        $result = [];
        foreach ($parameters as $param) {
            if (array_key_exists($param, $this->stores)) {
                $result[] = $param;
                continue;
            }
            if (strpos($param, $this->separator)) {
                $newParams = explode($this->separator, $param);
                $result[] = $newParams;
            } else {
                $result[] = $param;
            }
        }
        return $result;
    }
}
