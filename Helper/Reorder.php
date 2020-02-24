<?php

namespace Xigen\OrderHistory\Helper;

/**
 * Sales module base helper
 */
class Reorder extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_SALES_REORDER_ALLOW = 'sales/reorder/allow';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Xigen\OrderHistory\Model\HistoryFactory
     */
    protected $order;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Xigen\OrderHistory\Model\HistoryFactory $order
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Xigen\OrderHistory\Model\HistoryFactory $order
    ) {
        $this->order = $order;
        $this->customerSession = $customerSession;
        parent::__construct(
            $context
        );
    }

    /**
     * @return bool
     */
    public function isAllow()
    {
        return $this->isAllowed();
    }

    /**
     * Check if reorder is allowed for given store
     *
     * @param \Magento\Store\Model\Store|int|null $store
     * @return bool
     */
    public function isAllowed($store = null)
    {
        if ($this->scopeConfig->getValue(
            self::XML_PATH_SALES_REORDER_ALLOW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        )) {
            return true;
        }
        return false;
    }

    /**
     * Check is it possible to reorder
     *
     * @param int $orderId
     * @return bool
     */
    public function canReorder($orderId)
    {
        $order = $this->order->create()->load($orderId);
        if (!$order || !$order->getEntityId()) {
            return false;
        }

        if (!$this->isAllowed(1)) {
            return false;
        }

        if ($this->customerSession->isLoggedIn()) {
            return $order->canReorder();
        } else {
            return true;
        }
    }
}
