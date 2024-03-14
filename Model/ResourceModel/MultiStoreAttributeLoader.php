<?php
declare(strict_types=1);

namespace MageSuite\SeoHreflang\Model\ResourceModel;

class MultiStoreAttributeLoader
{
    protected array $cache = [];

    public function getAttributeRawValue(\Magento\Catalog\Model\AbstractModel $entity, $attributeCode, int $storeId = 0)
    {
        $entityId = (int)$entity->getId();

        if (!$entityId) {
            return null;
        }

        if (!isset($this->cache[$entityId][$attributeCode])) {
            $attribute = $entity->getResource()->getAttribute($attributeCode);
            if ($attribute) {
                $this->loadAttributeValues($attribute, $entityId);
            }
        }

        $value = $this->cache[$entityId][$attributeCode][$storeId] ?? null;
        if ($value === null) {
            $value = $this->cache[$entityId][$attributeCode][\Magento\Store\Model\Store::DEFAULT_STORE_ID] ?? null;
        }

        return $value;
    }

    protected function loadAttributeValues(\Magento\Eav\Model\Entity\Attribute\AbstractAttribute $attribute, int $entityId): void
    {
        $connection = $attribute->getResource()->getConnection();
        $table = $attribute->getBackend()->getTable();
        $entityIdField = $attribute->getEntity()->getLinkField();
        $select = $connection->select()
            ->from($table, ['value', 'store_id'])
            ->where($entityIdField.' = ?', $entityId)
            ->where('attribute_id = ?', $attribute->getId());
        $result = $connection->fetchAll($select);

        $this->cache[$entityId][$attribute->getAttributeCode()] = [];
        foreach ($result as $row) {
            $this->cache[$entityId][$attribute->getAttributeCode()][$row['store_id']] = $row['value'];
        }
    }
}
