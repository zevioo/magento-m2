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



class ShippingOption extends \Magento\Framework\View\Element\Html\Select
{
    /**
     * @return array
     */
    protected function _getOptions()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $deliveryModelConfig = $objectManager->create('\Magento\Shipping\Model\Config');
        $scopeConfig = $objectManager->create('\Magento\Framework\App\Config\ScopeConfigInterface');
        $deliveryMethods = $deliveryModelConfig->getActiveCarriers();
        $deliveryMethodsArray = array();
        foreach ($deliveryMethods as $shippigCode => $shippingModel) {
            $shippingTitle = $scopeConfig->getValue('carriers/'.$shippigCode.'/title');
            $deliveryMethodsArray[ $shippigCode] =$shippingTitle;
        }
       return $deliveryMethodsArray;
       
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * @return string
     */
    public function _toHtml()
    {
        if (!$this->getOptions()) {
            foreach ($this->_getOptions() as $groupId => $groupLabel) {
                $this->addOption($groupId, addslashes($groupLabel));
            }
        }

        return parent::_toHtml();
    }

    /**
     * @param string $optionValue
     * @return string
     */
    public function calcOptionHash($optionValue)
    {
        return sprintf('%u', crc32($this->getName().$this->getId().$optionValue));
    }

    /**
     * @param array      $option
     * @param bool|false $selected
     * @return string
     */
    protected function _optionToHtml($option, $selected = false)
    {
        $selectedHtml = $selected ? ' selected="selected"' : '';
        if ($this->getIsRenderToJsTemplate() === true) {
            $selectedHtml .= ' <%= option_extra_attrs.option_' . self::calcOptionHash($option['value']) . ' %>';
        }
        $html = '<option value="'.$this->escapeHtml($option['value']).'"'.$selectedHtml.'>'
            .$this->escapeHtml($option['label']).
            '</option>';

        return $html;
    }
}
