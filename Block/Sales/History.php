<?php

namespace Xigen\OrderHistory\Block\Sales;

/**
 * Order history sales block history class
 */
class History extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'Xigen_OrderHistory::sales/history.phtml';

    /**
     * @var \Xigen\OrderHistory\Model\ResourceModel\History\CollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_orderConfig;

    /**
     * @var \Xigen\OrderHistory\Model\ResourceModel\History\Collection
     */
    protected $orders;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * History constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Xigen\OrderHistory\Model\ResourceModel\History\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Xigen\OrderHistory\Model\HistoryFactory $history
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Xigen\OrderHistory\Model\ResourceModel\History\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Xigen\OrderHistory\Model\HistoryFactory $history,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->history = $history;
        $this->_storeManager = $storeManager;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Orders Pre'));
    }

    /**
     * Get store identifier
     * @return  int
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return bool|\Magento\Sales\Model\ResourceModel\Order\Collection
     */
    public function getOrders()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }

        if (!($customer = $this->getById($customerId))) {
            return false;
        }

        if (!$this->orders) {
            $this->orders = $this->_orderCollectionFactory
                ->create()
                ->addFieldToSelect(
                    '*'
                )
                ->addFieldToFilter(
                    'customer_email',
                    ['eq' => $customer->getEmail()]
                )
                ->addFieldToFilter(
                    'store_id',
                    ['in' => [1, 3, 4, 7, 8, 13, 14, 15, 16]]
                )
                ->addFieldToFilter(
                    'status',
                    ['in' => $this->_orderConfig->getVisibleOnFrontStatuses()]
                )
                ->setOrder(
                    'created_at',
                    'desc'
                );
        }

        return $this->orders;
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getOrders()) {
            $pager = $this->getLayout()->createBlock(
                \Magento\Theme\Block\Html\Pager::class,
                'sales.order.history.pager'
            )->setCollection(
                $this->getOrders()
            );
            $this->setChild('pager', $pager);
            $this->getOrders()->load();
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param object $order
     * @return string
     */
    public function getViewUrl($order)
    {
        return $this->getUrl('orderhistory/sales/view', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getTrackUrl($order)
    {
        return $this->getUrl('orderhistory/sales/track', ['order_id' => $order->getId()]);
    }

    /**
     * @param object $order
     * @return string
     */
    public function getReorderUrl($order)
    {
        return $this->getUrl('orderhistory/sales/reorder', ['order_id' => $order->getId()]);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    /**
     * Get customer by Id.
     * @param int $customerId
     * @return \Magento\Customer\Model\Data\Customer
     */
    public function getById($customerId)
    {
        try {
            return $this->customerRepositoryInterface->getById($customerId);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return false;
        }
    }
}
