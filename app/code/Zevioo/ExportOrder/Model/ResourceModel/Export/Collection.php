<?php

namespace Zevioo\ExportOrder\Model\ResourceModel\Export;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Zevioo\ExportOrder\Model\Export', 'Zevioo\ExportOrder\Model\ResourceModel\Export');
        $this->_map['fields']['page_id'] = 'main_table.page_id';
    }

}
?>