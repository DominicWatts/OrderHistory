<?php

namespace Xigen\OrderHistory\Model\ResourceModel\History;

/**
 * Order History history model resourceModel collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'entity_id';

    /**
     * Define resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Xigen\OrderHistory\Model\History::class,
            \Xigen\OrderHistory\Model\ResourceModel\History::class
        );
    }
}
