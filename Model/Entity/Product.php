<?php

namespace MageSuite\SeoHreflang\Model\Entity;

class Product implements EntityInterface
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
        return $this->registry->registry('product') ? true : false;
    }

    public function isActive($store)
    {
        $currentProduct = $this->registry->registry('product');

        if(!in_array($store->getId(), $currentProduct->getStoreIds())){
            return false;
        }

        $status = $currentProduct->getResource()->getAttributeRawValue($currentProduct->getId(), 'status', $store);

        return $status == \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED ? true : false;
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