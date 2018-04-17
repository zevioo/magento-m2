<?php

namespace Zevioo\ExportOrder\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Adapter\AdapterInterface;

class InstallSchema implements InstallSchemaInterface
{
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if (version_compare($context->getVersion(), '1.0.0') < 0){

    		$installer->run('create table order_export_information(
            order_id int not null auto_increment,
            order_send_data text,
            order_request_data text,
            created_at datetime,
            status int(6),
            primary key(order_id))');
        }

        $installer->endSetup();

    }
}