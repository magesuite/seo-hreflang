<?php
declare(strict_types=1);

namespace MageSuite\SeoHreflang\Model\ResourceModel;

class MultiStoreUrlRewriteLoader extends \Magento\UrlRewrite\Model\Storage\DbStorage
{
    protected array $cache = [];

    public function findOneByData(array $data)
    {
        $targetPath = $data['target_path'] ?? null;

        if (!$targetPath) {
            return null;
        }

        $storeId = $data['store_id'] ?? \Magento\Store\Model\Store::DEFAULT_STORE_ID;

        if (!isset($this->cache[$targetPath])) {
            $this->preloadUrlRewrites($data);
        }

        return $this->cache[$targetPath][$storeId] ?? null;
    }

    public function preloadUrlRewrites($data): void
    {
        unset($data['store_id']);

        $urlRewrites = $this->findAllByData($data);

        foreach ($urlRewrites as $urlRewrite) {
            $this->cache[$urlRewrite->getTargetPath()][$urlRewrite->getStoreId()] = $urlRewrite;
        }
    }
}
