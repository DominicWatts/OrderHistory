<?php

namespace Xigen\OrderHistory\Api\Data;

/**
 * Interface HistoryItemSearchResultsInterface
 */
interface HistoryItemSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get HistoryItem list.
     * @return \Xigen\OrderHistory\Api\Data\HistoryItemInterface[]
     */
    public function getItems();

    /**
     * Set item list.
     * @param \Xigen\OrderHistory\Api\Data\HistoryItemInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
