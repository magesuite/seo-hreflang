<?php

namespace MageSuite\SeoHreflang\Model;

class EntityPool
{
    protected $entities;

    public function __construct(array $entities)
    {
        $this->entities = $entities;
    }

    public function getEntity()
    {
        foreach($this->entities as $entity){
            if(!$entity->isApplicable()){
                continue;
            }

            return $entity;
        }

        return null;
    }
}
