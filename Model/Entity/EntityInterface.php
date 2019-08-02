<?php

namespace MageSuite\SeoHreflang\Model\Entity;

interface EntityInterface
{
    /**
     * @return bool
     */
    public function isApplicable();

    /**
     * @param object $store
     * @return bool
     */
    public function isActive($store);

    /**
     * @param object $store
     * @return string
     */
    public function getUrl($store);
}
