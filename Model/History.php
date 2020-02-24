<?php

namespace Xigen\OrderHistory\Model;

use Magento\Catalog\Model\Product\Type;
use Magento\Framework\Api\DataObjectHelper;
use Xigen\OrderHistory\Api\Data\HistoryInterface;
use Xigen\OrderHistory\Api\Data\HistoryInterfaceFactory;

/**
 * Order history model history class
 */
class History extends \Magento\Framework\Model\AbstractModel
{
    const STATUS_CANCELED = 'canceled';
    const STATUS_AWAITING_STOCK = 'awaiting_stock';
    const STATUS_BEING_PICKED = 'being_picked';
    const STATUS_CLOSED = 'closed';
    const STATUS_COMPLETE = 'complete';
    const STATUS_FRAUD = 'fraud';
    const STATUS_FULL_REFUND = 'full_refund';
    const STATUS_HOLDED = 'holded';
    const STATUS_PARTIAL_REFUND = 'partial_refund';
    const STATUS_PART_SHIPPED = 'part_shipped';
    const STATUS_PAYMENT_REVIEW = 'payment_review';
    const STATUS_PAYPAL_REVERSED = 'paypal_reversed';
    const STATUS_PENDING = 'pending';
    const STATUS_PENDING_PAYMENT = 'pending_payment';
    const STATUS_PROCESSING = 'processing';
    const STATUS_PAYPAL_CANCELED_REVERSAL = 'paypal_canceled_reversal';
    const STATUS_PENDING_PAYPAL = 'pending_paypal';

    const STATE_PAYMENT_REVIEW = 'payment_review';
    const STATE_HOLDED = 'holded';

    /**
     * @var HistoryInterfaceFactory
     */
    protected $historyDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    protected $_eventPrefix = 'm1_sales_flat_order';

    /**
     * @var ResourceModel\HistoryItem\CollectionFactory
     */
    protected $_orderItemCollectionFactory;

    /**
     * @var ResourceModel\HistoryAddress\CollectionFactory
     */
    protected $_addressCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productListFactory;

    /**
     * History constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param HistoryInterfaceFactory $historyDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param ResourceModel\History $resource
     * @param ResourceModel\History\Collection $resourceCollection
     * @param ResourceModel\HistoryItem\CollectionFactory $orderItemCollectionFactory
     * @param ResourceModel\HistoryAddress\CollectionFactory $addressCollectionFactory
     * @param Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        HistoryInterfaceFactory $historyDataFactory,
        DataObjectHelper $dataObjectHelper,
        \Xigen\OrderHistory\Model\ResourceModel\History $resource,
        \Xigen\OrderHistory\Model\ResourceModel\History\Collection $resourceCollection,
        \Xigen\OrderHistory\Model\ResourceModel\HistoryItem\CollectionFactory $orderItemCollectionFactory,
        \Xigen\OrderHistory\Model\ResourceModel\HistoryAddress\CollectionFactory $addressCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productListFactory,
        array $data = []
    ) {
        $this->historyDataFactory = $historyDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->_orderItemCollectionFactory = $orderItemCollectionFactory;
        $this->_addressCollectionFactory = $addressCollectionFactory;
        $this->_storeManager = $storeManager;
        $this->productListFactory = $productListFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Retrieve history model with history data
     * @return HistoryInterface
     */
    public function getDataModel()
    {
        $historyData = $this->getData();

        $historyDataObject = $this->historyDataFactory->create();
        $this->dataObjectHelper->populateWithArray(
            $historyDataObject,
            $historyData,
            HistoryInterface::class
        );

        return $historyDataObject;
    }

    /**
     * Retrieve billing agreement status label
     *
     * @return \Magento\Framework\Phrase|string
     */
    public function getStatusLabel()
    {
        switch ($this->getStatus()) {
            case self::STATUS_CANCELED:
                return __('Canceled');
            case self::STATUS_AWAITING_STOCK:
                return __('Awaiting Stock');
            case self::STATUS_BEING_PICKED:
                return __('Being Picked in Warehouse');
            case self::STATUS_CLOSED:
                return __('Closed');
            case self::STATUS_COMPLETE:
                return __('Completed');
            case self::STATUS_FRAUD:
                return __('Suspected Fraud');
            case self::STATUS_FULL_REFUND:
                return __('Full Refund');
            case self::STATUS_HOLDED:
                return __('On Hold');
            case self::STATUS_PARTIAL_REFUND:
                return __('Partial Refund');
            case self::STATUS_PART_SHIPPED:
                return __('Part Shipped');
            case self::STATUS_PAYMENT_REVIEW:
                return __('Payment Review');
            case self::STATUS_PAYPAL_REVERSED:
                return __('Paypal Reversed');
            case self::STATUS_PENDING:
                return __('Payment Failed');
            case self::STATUS_PENDING_PAYMENT:
                return __('Order Not Confirmed');
            case self::STATUS_PROCESSING:
                return __('Payment Completed');
            case self::STATUS_PAYPAL_CANCELED_REVERSAL:
                return __('PayPal Canceled Reversal');
            case self::STATUS_PENDING_PAYPAL:
                return __('Pending PayPal');
            default:
                return '';
        }
    }

    /**
     * Get items collection
     * @param bool $sku
     * @return ItemCollection
     */
    public function getItemsCollection($sku = false)
    {
        $collection = $this->_orderItemCollectionFactory
            ->create()
            ->addFieldToSelect(
                '*'
            )
            ->addFieldToFilter(
                'order_id',
                ['eq' => $this->getId()]
            )
            ->addFieldToFilter(
                'product_type',
                ['eq' => Type::TYPE_SIMPLE]
            )
            ->setOrder(
                'created_at',
                'item_id'
            );

        if ($sku) {
            $collection->addFieldToFilter(
                'sku',
                ['eq' => $sku]
            );
            return $collection->getFirstItem();
        }

        return $collection;
    }

    /**
     * Get addresses
     * @return \Xigen\OrderHistory\Api\Data\HistoryAddressInterface[]
     */
    public function getAddresses()
    {
        if ($this->getData('addresses') == null) {
            $this->setData(
                'addresses',
                $this->getAddressesCollection()->getItems()
            );
        }
        return $this->getData('addresses');
    }

    /**
     * Get addresses collection
     * @return Collection
     */
    public function getAddressesCollection()
    {
        $collection = $this->_addressCollectionFactory
            ->create()
            ->addFieldToSelect(
                '*'
            )
            ->addFieldToFilter(
                'parent_id',
                ['eq' => $this->getId()]
            )
            ->setOrder(
                'entity_id'
            );

        return $collection;
    }

    /**
     * Get address by id
     * @param mixed $addressId
     * @return false
     */
    public function getAddressById($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId() == $addressId) {
                return $address;
            }
        }
        return false;
    }

    /**
     * Retrieve order billing address
     * @return \Xigen\OrderHistory\Api\Data\HistoryAddressInterface|null
     */
    public function getBillingAddress()
    {
        foreach ($this->getAddresses() as $address) {
            if ($address->getAddressType() == 'billing' && !$address->isDeleted()) {
                return $address;
            }
        }
        return null;
    }

    /**
     * Retrieve order shipping address
     * @return \Xigen\OrderHistory\Model\HistoryAddress|null
     */
    public function getShippingAddress()
    {
        foreach ($this->getAddresses() as $address) {
            if ($address->getAddressType() == 'shipping' && !$address->isDeleted()) {
                return $address;
            }
        }
        return null;
    }

    /**
     * Retrieve store model instance - hardcode to JAS english
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_storeManager->getStore(1);
    }

    /**
     * Retrieve order reorder availability
     *
     * @return bool
     */
    public function canReorder()
    {
        return $this->_canReorder();
    }

    /**
     * Retrieve order reorder availability
     * @return bool
     */
    protected function _canReorder()
    {
        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }

        $products = [];
        $itemsCollection = $this->getItemsCollection();
        foreach ($itemsCollection as $item) {
            $products[] = $item->getSku();
        }

        if (!empty($products)) {
            $productsCollection = $this->productListFactory
                ->create()
                ->setStoreId(
                    $this->_storeManager->getStore()->getId()
                )
                ->addFieldToFilter(
                    'sku',
                    ['in' => $products]
                )
                ->addAttributeToSelect('status')
                ->load();

            foreach ($productsCollection as $product) {
                if (!$product->isSalable()) {
                    return false;
                }
            }
        }

        return $productsCollection;
    }

    /**
     * Check whether the payment is in payment review state
     * In this state order cannot be normally processed. Possible actions can be:
     * - accept or deny payment
     * - fetch transaction information
     *
     * @return bool
     */
    public function isPaymentReview()
    {
        return $this->getState() === self::STATE_PAYMENT_REVIEW;
    }

    /**
     * Retrieve order unhold availability
     * @return bool
     */
    public function canUnhold()
    {
        return $this->getState() === self::STATE_HOLDED;
    }
}
