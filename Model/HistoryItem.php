<?php

namespace Xigen\OrderHistory\Model;

use Magento\Framework\Api\DataObjectHelper;
use Xigen\OrderHistory\Api\Data\HistoryItemInterface;
use Xigen\OrderHistory\Api\Data\HistoryItemInterfaceFactory;

/**
 * Order history model history item class
 */
class HistoryItem extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var HistoryItemInterfaceFactory
     */
    protected $historyitemDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    protected $_eventPrefix = 'm1_sales_flat_order_item';

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param HistoryItemInterfaceFactory $historyitemDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Xigen\OrderHistory\Model\ResourceModel\HistoryItem $resource
     * @param \Xigen\OrderHistory\Model\ResourceModel\HistoryItem\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        HistoryItemInterfaceFactory $historyitemDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Xigen\OrderHistory\Model\ResourceModel\HistoryItem $resource,
        \Xigen\OrderHistory\Model\ResourceModel\HistoryItem\Collection $resourceCollection,
        array $data = []
    ) {
        $this->historyitemDataFactory = $historyitemDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve historyitem model with historyitem data
     * @return HistoryItemInterface
     */
    public function getDataModel()
    {
        $historyitemData = $this->getData();

        $historyitemDataObject = $this->historyitemDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $historyitemDataObject,
            $historyitemData,
            HistoryItemInterface::class
        );

        return $historyitemDataObject;
    }
}
