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
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Xigen\OrderHistory\Helper\Config
     */
    protected $configHelper;

    /**
     * Undocumented function
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Xigen\OrderHistory\Model\ResourceModel\History\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Xigen\OrderHistory\Model\HistoryFactory $history
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Xigen\OrderHistory\Helper\Config $configHelper
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
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Xigen\OrderHistory\Helper\Config $configHelper,
        array $data = []
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->history = $history;
        $this->_storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->configHelper = $configHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Previous Orders'));
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

        $currentMagento2Store = $this->getStoreId();
        $storeMappings = $this->configHelper->getStoreIdMappings();
        $historyArray = [];
        foreach ($storeMappings as $mage1storeId => $mage2storeId) {
            if ($mage2storeId == $currentMagento2Store) {
                $historyArray[] = $mage1storeId;
            }
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
                    ['in' => [$historyArray]]
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
     * Format price without symbol
     * @param float $price
     * @return float
     */
    public function formatPrice($price, $currency)
    {
        return $this->currencyFactory
            ->create()
            ->format(
                $price,
                ['symbol' => $this->getCurrencySymbol($currency)],
                false
            );
    }

    /**
     * Get currency from loaded order
     * @return void|string
     */
    public function getCurrencySymbol($currencyCode)
    {
        $order = $this->getOrder();

        $currency = $this->currencyFactory
            ->create()
            ->load($currencyCode);

        if (!$currency) {
            return null;
        }

        return $currency->getCurrencySymbol();
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

    /**
     * Get message for no orders.
     * @return \Magento\Framework\Phrase
     */
    public function getEmptyOrdersMessage()
    {
        return __('You have placed no orders.');
    }
}
