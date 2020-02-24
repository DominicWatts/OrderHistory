<?php

namespace Xigen\OrderHistory\Api\Data;

/**
 * Interface HistoryAddressSearchResultsInterface
 */
interface HistoryAddressSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{

    /**
     * Get HistoryAddress list.
     * @return \Xigen\OrderHistory\Api\Data\HistoryAddressInterface[]
     */
    public function getItems();

    /**
     * Set item list.
     * @param \Xigen\OrderHistory\Api\Data\HistoryAddressInterface[] $items
     * @return $this
     */
    public function setItems(array $items);
}
