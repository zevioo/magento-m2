<?php
namespace Zevioo\ExportOrder\Model\ResourceModel;

class Export extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('order_export_information', 'order_id');
    }
}
?>