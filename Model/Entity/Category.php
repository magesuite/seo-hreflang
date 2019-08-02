<?php

namespace MageSuite\SeoHreflang\Model\Entity;

class Category implements EntityInterface
{
    /**
     * @var \Magento\UrlRewrite\Model\UrlFinderInterface
     */
    protected $urlFinder;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    public function __construct(
        \Magento\UrlRewrite\Model\UrlFinderInterface $urlFinder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Registry $registry
    ){
        $this->urlFinder = $urlFinder;
        $this->request = $request;
        $this->registry = $registry;
    }

    public function isApplicable()
    {
        $currentProduct = $this->registry->registry('product');
        $currentCategory = $this->registry->registry('current_category');

        return !$currentProduct && $currentCategory ? true : false;
    }

    public function isActive($store)
    {
        $currentCategory = $this->registry->registry('current_category');

        return (bool)$currentCategory->getResource()->getAttributeRawValue($currentCategory->getId(), 'is_active', $store);
    }

    public function getUrl($store)
    {
        $urlRewrite = $this->urlFinder->findOneByData([
            'target_path' => trim($this->request->getPathInfo(), '/'),
            'store_id' => $store->getId()
        ]);

        if($urlRewrite){
            return $store->getBaseUrl() . $urlRewrite->getRequestPath();
        }

        return $store->getCurrentUrl(false);
    }

}