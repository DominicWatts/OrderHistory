<?php

namespace Xigen\OrderHistory\Controller\Sales;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;

/**
 * Order history sales view controller class
 */
class View extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Xigen\OrderHistory\Model\OrderFactory
     */
    protected $orderFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;

    /**
     * @var ForwardFactory
     */
    protected $resultForwardFactory;

    /**
     * @var RedirectFactory
     */
    protected $redirectFactory;

    /**
     * @var Http
     */
    private $request;

    /**
     * View constructor.
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Xigen\OrderHistory\Model\HistoryFactory $orderFactory
     * @param Registry $registry
     * @param \Magento\Framework\UrlInterface $url
     * @param ForwardFactory $resultForwardFactory
     * @param RedirectFactory $redirectFactory
     * @param Http $request
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Xigen\OrderHistory\Model\HistoryFactory $orderFactory,
        Registry $registry,
        \Magento\Framework\UrlInterface $url,
        ForwardFactory $resultForwardFactory,
        RedirectFactory $redirectFactory,
        Http $request,
        CustomerSession $customerSession
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->orderFactory = $orderFactory;
        $this->registry = $registry;
        $this->url = $url;
        $this->resultForwardFactory = $resultForwardFactory;
        $this->redirectFactory = $redirectFactory;
        $this->request = $request;
        $this->customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->customerSession->isLoggedIn()) {
            $this->_redirect('customer/account/login');
        }

        $orderId = (int) $this->request->getParam('order_id');
        if (!$orderId) {
            /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
            $resultForward = $this->resultForwardFactory->create();
            return $resultForward->forward('noroute');
        }

        $order = $this->orderFactory->create()->load($orderId);

        if ($order && $order->getId()) {
            $this->registry->register('current_order', $order);
            $page = $this->resultPageFactory->create();
            $page->getConfig()->addBodyClass('sales-order-view');
            return $page;
        }

        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->redirectFactory->create();
        return $resultRedirect->setUrl($this->url->getUrl('*/*/history'));
    }
}
