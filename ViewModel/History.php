<?php

namespace Xigen\OrderHistory\ViewModel;

use Magento\Contact\Helper\Data;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Xigen\OrderHistory\Helper\Reorder;

/**
 * Provides access to helper functions on the history grid.
 */
class History implements ArgumentInterface
{
    /**
     * @var PostHelper
     */
    private $postHelper;

    /**
     * @var Reorder
     */
    private $reorderHelper;

    /**
     * History constructor.
     * @param PostHelper $postHelper
     * @param Reorder $reorderHelper
     */
    public function __construct(
        PostHelper $postHelper,
        Reorder $reorderHelper
    ) {
        $this->postHelper = $postHelper;
        $this->reorderHelper = $reorderHelper;
    }

    /**
     * Can reorder by entity ID helper function
     * @param $entityId
     * @return bool
     */
    public function canReorder($entityId)
    {
        return $this->reorderHelper->canReorder($entityId);
    }

    /**
     * PostData helper by URL helper function
     * @param $reorderUrl
     * @return string
     */
    public function getPostData($reorderUrl)
    {
        return $this->postHelper->getPostData($reorderUrl);
    }
}
