<?php

namespace Xigen\OrderHistory\Block\Adminhtml\System;

class Store extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray
{
    const ENTITY_TYPE_CODE = 'catalog_product';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $_template = 'Xigen_OrderHistory::system/config/form/field/array.phtml';

    /**
     * Store constructor.
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * Columns
     * @return void
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'store_id',
            ['label' => __('Magento 2 ID')]
        );
        $this->addColumn(
            'store_id_mapping',
            ['label' => __('Magento 1 ID Mapping')]
        );
        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Store ID Mapping');
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

    /**
     * @param string $columnName
     * @return string
     * @throws \Exception
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName == 'store_id') {
            $inputName = $this->_getCellInputElementName($columnName);
            $rendered = __(
                '<select style="width:160px;" id="%1" name="%2">',
                $this->_getCellInputElementId(
                    '<%- _id %>',
                    $columnName
                ),
                $inputName
            );
            $stores = $this->storeManager->getStores();
            foreach ($stores as $store) {
                $rendered .= __(
                    '<option value="%1">[%2] %3</option>',
                    $store->getStoreId(),
                    $store->getStoreId(),
                    $store->getName()
                );
            }

            $rendered .= '</select>';
            return $rendered;
        }

        return parent::renderCellTemplate($columnName);
    }
}
