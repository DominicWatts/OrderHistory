<?php

namespace Xigen\OrderHistory\Model\ResourceModel;

/**
 * Order History history model resourceModel
 */
class History extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('m1_sales_flat_order', 'entity_id');
    }
}
