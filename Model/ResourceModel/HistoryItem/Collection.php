<?php

namespace Xigen\OrderHistory\Model\ResourceModel\HistoryItem;

/**
 * Order history history item model resourceModel collection
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Xigen\OrderHistory\Model\History
     */
    public $_salesOrder;

    /**
     * @var string
     */
    protected $_idFieldName = 'item_id';

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField = 'order_id';

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

    /**
     * Set sales order model as parent collection object
     *
     * @param \Xigen\OrderHistory\Model\History $order
     * @return $this
     */
    public function setSalesOrder($order)
    {
        $this->_salesOrder = $order;
        return $this;
    }

    /**
     * Retrieve sales order as parent collection object
     *
     * @return \Xigen\OrderHistory\Model\History|null
     */
    public function getSalesOrder()
    {
        return $this->_salesOrder;
    }

    /**
     * Add order filter
     *
     * @param int|\Magento\Sales\Model\Order|array $order
     * @return $this
     */
    public function setOrderFilter($order)
    {
        if ($order instanceof \Xigen\OrderHistory\Model\History) {
            $this->setSalesOrder($order);
            $orderId = $order->getId();
            if ($orderId) {
                $this->addFieldToFilter($this->_orderField, $orderId);
            } else {
                $this->_totalRecords = 0;
                $this->_setIsLoaded(true);
            }
        } else {
            $this->addFieldToFilter($this->_orderField, $order);
        }
        return $this;
    }
}
