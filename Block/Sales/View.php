<?php

namespace Xigen\OrderHistory\Block\Sales;

use Magento\Framework\Currency;

/**
 * Order history salese block view class
 */
class View extends \Magento\Framework\View\Element\Template
{
    const THUMBNAIL_DIMENSIONS = 100;

    /**
     * @var string
     */
    protected $_template = 'Xigen_OrderHistory::sales/view.phtml';

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
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var \Magento\Customer\Model\Address\Config
     */
    protected $addressConfig;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepositoryInterface;

    /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $imageHelper;

    /**
     * View constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Xigen\OrderHistory\Model\ResourceModel\History\CollectionFactory $orderCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Xigen\OrderHistory\Model\HistoryFactory $history
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Customer\Model\Address\Config $addressConfig
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface
     * @param \Magento\Catalog\Helper\Image $imageHelper
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Xigen\OrderHistory\Model\ResourceModel\History\CollectionFactory $orderCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Xigen\OrderHistory\Model\HistoryFactory $history,
        \Magento\Framework\Registry $registry,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepositoryInterface,
        \Magento\Catalog\Helper\Image $imageHelper,
        array $data = []
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_orderConfig = $orderConfig;
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->history = $history;
        $this->_coreRegistry = $registry;
        $this->currencyFactory = $currencyFactory;
        $this->addressConfig = $addressConfig;
        $this->productRepositoryInterface = $productRepositoryInterface;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        if ($order = $this->getOrder()) {
            $this->pageConfig->getTitle()->set(__('Order # %1', $order->getIncrementId()));
        }
    }

    /**
     * Retrieve current order model instance
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }

    /**
     * Format price without symbol
     * @param float $price
     * @return float
     */
    public function formatPrice($price)
    {
        return $this->currencyFactory
            ->create()
            ->format(
                $price,
                ['symbol' => $this->getCurrencySymbol()],
                false
            );
    }

    /**
     * Get currency from loaded order
     * @return void|string
     */
    public function getCurrencySymbol()
    {
        $order = $this->getOrder();
        if (!$order || ! $order->getId()) {
            return null;
        }

        $currency = $this->currencyFactory
            ->create()
            ->load($order->getOrderCurrency());

        if (!$currency) {
            return null;
        }

        return $currency->getCurrencySymbol();
    }

    /**
     * Format address in a specific way
     *
     * @param Address $address
     * @param string $type
     * @return string|null
     */
    public function addressFormat($address, $type)
    {
        $this->addressConfig
            ->setStore($this->getOrder()->getStoreId());

        $formatType = $this->addressConfig->getFormatByCode($type);
        if (!$formatType || !$formatType->getRenderer()) {
            return null;
        }
        return $formatType->getRenderer()
            ->renderArray($address->getData());
    }

    /**
     * Returns string with formatted address
     * @param \Xigen\OrderHistory\Model\HistoryAddress $address
     * @return null|string
     */
    public function getFormattedAddress($address)
    {
        return $this->addressFormat($address, 'html');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('orderhistory/sales/history');
    }

    /**
     * Get product by SKU
     * @param $sku
     * @param bool $editMode
     * @param null $storeId
     * @param bool $forceReload
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface
     */
    public function getProduct($sku, $editMode = false, $storeId = null, $forceReload = false)
    {
        try {
            return $this->productRepositoryInterface->get($sku, $editMode, $storeId, $forceReload);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return false;
        }
    }

    /**
     * Get product image
     * @param string $sku
     * @return string
     */
    public function getProductImage($sku)
    {
        $product = $this->getProduct($sku);
        if (!$product && !$product->getId()) {
            return false;
        }
        return $this->imageHelper->init(
            $product,
            'product_page_image_small'
        )->setImageFile(
            $product->getFile()
        )->resize(
            self::THUMBNAIL_DIMENSIONS,
            self::THUMBNAIL_DIMENSIONS
        )->getUrl();
    }
}
