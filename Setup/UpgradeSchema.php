<?php

namespace MageSuite\SeoHreflang\Setup;

class UpgradeSchema implements \Magento\Framework\Setup\UpgradeSchemaInterface
{
    public function upgrade(
        \Magento\Framework\Setup\SchemaSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '0.0.2', '<')) {
            if (!$setup->getConnection()->tableColumnExists($setup->getTable('store'), 'hreflang_code')) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('store'),
                    'hreflang_code',
                    [
                        'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                        'length' => 255,
                        'comment' => 'Hreflang Code'
                    ]
                );
            }
        }

        $setup->endSetup();
    }
}
