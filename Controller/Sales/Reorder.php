<?php

namespace Xigen\OrderHistory\Controller\Sales;

use Magento\Framework\Controller\ResultFactory;

/**
 * Class Reorder sales controller class
 */
class Reorder extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Xigen\OrderHistory\Helper\Reorder
     */
    protected $reorder;

    /**
     * Undocumented function
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Xigen\OrderHistory\Helper\Reorder $reorder
     * @param \Xigen\OrderHistory\Model\HistoryFactory $order
     * @param \Xigen\OrderHistory\Api\HistoryRepositoryInterface $orderRepository
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Xigen\OrderHistory\Helper\Reorder $reorder,
        \Xigen\OrderHistory\Model\HistoryFactory $order,
        \Xigen\OrderHistory\Api\HistoryRepositoryInterface $orderRepository,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->resultPageFactory = $resultPageFactory;
        $this->order = $order;
        $this->reorder = $reorder;
        $this->cart = $cart;
        $this->productFactory = $productFactory;
        parent::__construct($context);
    }

    /**
     * Reorder controller
     * @return \Magento\Backend\Model\View\Result\Redirect|\Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute()
    {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $orderId = $this->getRequest()->getParam('order_id');

        /** @var \Xigen\OrderHistory\Model\History $order */
        $order = $this->orderRepository->get($orderId);
        $products = $this->reorder->canReorder($order->getEntityId());
        if (!$products || !$products->getSize()) {
            $this->messageManager->addErrorMessage(
                __('One or more products are no longer available.')
            );
            $resultRedirect->setPath('orderhistory/sales/history');
            return $resultRedirect;
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if (!$order->getEntityId()) {
            $this->messageManager->addErrorMessage(
                __('Problem loading order.')
            );
            $resultRedirect->setPath('orderhistory/sales/history');
            return $resultRedirect;
        }

        // used to fetch quantity in original order
        $model = $this->order->create()->load($orderId);

        foreach ($products as $product) {
            $history = $model->getItemsCollection($product->getSku());
            $qty = (int) $history->getQtyOrdered();
            if ($qty === 0) {
                $this->messageManager->addWarning(
                    __('Cannot add %1 at this time', $add->getName())
                );
                continue;
            }

            // Add the product to the cart or update the quantity
            try {
                $add = $this->productFactory->create()->load($product->getId());
                if (!$add) {
                    $this->messageManager->addWarnng(
                        __('Cannot add %1 x %2 at this time', $qty, $add->getName())
                    );
                    continue;
                }
                $productName = $product->getName();
                $this->cart->addProduct(
                    $add,
                    $qty
                );
                $this->messageManager->addSuccess(
                    __('Added %1 x %2', $qty, $add->getName())
                );
            } catch (\Exception $e) {
                $this->messageManager->addException(
                    $e,
                    __("%1 %2", $product->getName(), $e->getMessage())
                );
            }

            // Save the cart
            $this->cart->save();
        }

        $resultRedirect->setPath('orderhistory/sales/history');
        return $resultRedirect;
    }
}
