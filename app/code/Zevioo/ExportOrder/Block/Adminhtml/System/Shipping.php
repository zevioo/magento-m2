<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-seo
 * @version   1.0.62
 * @copyright Copyright (C) 2017 Mirasvit (https://mirasvit.com/)
 */



namespace Zevioo\ExportOrder\Block\Adminhtml\System;

class Shipping extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    /**
     * @var NoindexOption
     */
    protected $optionsRenderer;

    /**
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getOptionsRenderer()
    {
        if (!$this->optionsRenderer) {
            $this->optionsRenderer = $this->getLayout()->createBlock(
                'Zevioo\ExportOrder\Block\Adminhtml\System\ShippingOption',
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->optionsRenderer->setClass('customer_options_select');
            $this->optionsRenderer->setExtraParams('style="width:150px"');
        }

        return $this->optionsRenderer;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $select = $this->_getOptionsRenderer();
        $this->addColumn('shipping_code', [
            'label' => __('Shipping Method'),
            'renderer' => $select,
        ]);
        $this->addColumn('delivery_delay', [
            'label' => __('Delay in Days'),
            'style' => 'width:250px',
        ]);
       

        
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');
        parent::_construct();
    }


    /**
     * @param \Magento\Framework\DataObject $row
     *
     * @return void
     */
    protected function _prepareArrayRow(\Magento\Framework\DataObject $row)
    {
        $options = [];
        if ($row->getShippingCode()) {
            $options['option_' . $this->_getOptionsRenderer()->calcOptionHash($row->getData('shipping_code'))]
                = 'selected="selected"';
        }

        $row->setData('option_extra_attrs', $options);
    }
}
