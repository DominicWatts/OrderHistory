<?php

namespace Xigen\OrderHistory\Model\ResourceModel;

/**
 * Order History history address model resourceModel
 */
class HistoryAddress extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('m1_sales_flat_order_address', 'entity_id');
    }
}
