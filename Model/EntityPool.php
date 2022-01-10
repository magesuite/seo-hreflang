<?php
declare(strict_types=1);

namespace MageSuite\SeoHreflang\Model;

class EntityPool
{
    protected $entities;

    public function __construct(array $entities = [])
    {
        $this->entities = $entities;
    }

    public function getEntity(): ?\MageSuite\SeoHreflang\Model\Entity\EntityInterface
    {
        foreach ($this->entities as $entity) {
            if (!$entity->isApplicable()) {
                continue;
            }

            return $entity;
        }

        return null;
    }
}
