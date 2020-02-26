<?php

namespace Xigen\OrderHistory\Block\Adminhtml\System;

class Status extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    const ENTITY_TYPE_CODE = 'catalog_product';

    /**
     * @var string
     */
    protected $_template = 'Xigen_OrderHistory::system/config/form/field/array.phtml';

    /**
     * Columns
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'status_id',
            ['label' => __('Magento 1 Status Code')]
        );
        $this->addColumn(
            'status_id_mapping',
            ['label' => __('Magento 1 Status Label')]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Status Mapping');
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Exception
     */
    protected function _getElementHtml(
        \Magento\Framework\Data\Form\Element\AbstractElement $element
    ) {
        $this->setElement($element)
            ->setExtraParams('style="width:1000px;"');
        $html = $this->_toHtml();
        $this->_arrayRowsCache = null;
        return $html;
    }
}
