<?php

namespace Xigen\OrderHistory\Api\Data;

/**
 * Interface HistorySearchResultsInterface
 */
interface HistorySearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get History list.
     * @return \Xigen\OrderHistory\Api\Data\HistoryInterface[]
     */
    public function getItems();

    /**
     * Set item list.
     * @param \Xigen\OrderHistory\Api\Data\HistoryInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
