<?php

declare(strict_types=1);

namespace MageSuite\SeoHreflang\Setup\Patch\Data;

class MigrateSeoConfig implements \Magento\Framework\Setup\Patch\DataPatchInterface
{
    protected array $fields = [
        'hreflang_tags_enabled',
        'hreflang_scope',
        'hreflang_disable_on_non_canonical',
        'x_default',
        'hide_for_noindex',
        'strip_parameters_from_the_url',
        'exclude_store',
        'remove_trailing_slash'
    ];

    public function __construct(
        protected \Magento\Framework\App\ResourceConnection $resource,
    ) {
    }

    public function apply()
    {
        $connection = $this->resource->getConnection();
        $table = $this->resource->getTableName('core_config_data');

        foreach ($this->fields as $field) {
            $connection->update(
                $table,
                ['path' => 'seo/hreflang/' . $field],
                ['path = ?' => 'seo/configuration/' . $field]
            );
        }
    }

    public static function getDependencies()
    {
        return [];
    }

    public function getAliases()
    {
        return [];
    }
}
