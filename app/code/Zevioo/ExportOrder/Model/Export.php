<?php
namespace Zevioo\ExportOrder\Model;

class Export extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Zevioo\ExportOrder\Model\ResourceModel\Export');
    }
}
?>