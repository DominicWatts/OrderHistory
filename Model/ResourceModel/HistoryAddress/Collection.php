<?php

namespace Xigen\OrderHistory\Model\ResourceModel\HistoryAddress;

/**
 * Order history history address model resourceModel collection
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
            \Xigen\OrderHistory\Model\HistoryAddress::class,
            \Xigen\OrderHistory\Model\ResourceModel\HistoryAddress::class
        );
    }
}
