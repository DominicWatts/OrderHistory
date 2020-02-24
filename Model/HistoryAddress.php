<?php

namespace Xigen\OrderHistory\Model;

use Magento\Framework\Api\DataObjectHelper;
use Xigen\OrderHistory\Api\Data\HistoryAddressInterface;
use Xigen\OrderHistory\Api\Data\HistoryAddressInterfaceFactory;

/**
 * Order history model history address class
 */
class HistoryAddress extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var HistoryAddressInterfaceFactory
     */
    protected $historyaddressDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    protected $_eventPrefix = 'm1_sales_flat_order_address';

    /**
     * HistoryAddress constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param HistoryAddressInterfaceFactory $historyaddressDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\HistoryAddress $resource
     * @param ResourceModel\HistoryAddress\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        HistoryAddressInterfaceFactory $historyaddressDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Xigen\OrderHistory\Model\ResourceModel\HistoryAddress $resource,
        \Xigen\OrderHistory\Model\ResourceModel\HistoryAddress\Collection $resourceCollection,
        array $data = []
    ) {
        $this->historyaddressDataFactory = $historyaddressDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve historyaddress model with historyaddress data
     * @return HistoryAddressInterface
     */
    public function getDataModel()
    {
        $historyaddressData = $this->getData();

        $historyaddressDataObject = $this->historyaddressDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $historyaddressDataObject,
            $historyaddressData,
            HistoryAddressInterface::class
        );

        return $historyaddressDataObject;
    }
}
