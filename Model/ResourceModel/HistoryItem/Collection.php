<?php

namespace Xigen\OrderHistory\Model\ResourceModel\HistoryItem;

/**
 * Order history history item model resourceModel collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * @var string
     */
    protected $_idFieldName = 'item_id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            \Xigen\OrderHistory\Model\HistoryItem::class,
            \Xigen\OrderHistory\Model\ResourceModel\HistoryItem::class
        );
    }
}
