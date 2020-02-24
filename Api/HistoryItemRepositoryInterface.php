<?php

namespace Xigen\OrderHistory\Api;

/**
 * Interface HistoryItemRepositoryInterface
 */
interface HistoryItemRepositoryInterface
{

    /**
     * Save HistoryItem
     * @param \Xigen\OrderHistory\Api\Data\HistoryItemInterface $historyItem
     * @return \Xigen\OrderHistory\Api\Data\HistoryItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Xigen\OrderHistory\Api\Data\HistoryItemInterface $historyItem
    );

    /**
     * Retrieve HistoryItem
     * @param string $itemId
     * @return \Xigen\OrderHistory\Api\Data\HistoryItemInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($itemId);

    /**
     * Retrieve HistoryItem matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Xigen\OrderHistory\Api\Data\HistoryItemSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete HistoryItem
     * @param \Xigen\OrderHistory\Api\Data\HistoryItemInterface $historyItem
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Xigen\OrderHistory\Api\Data\HistoryItemInterface $historyItem
    );

    /**
     * Delete HistoryItem by ID
     * @param string $itemId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($itemId);
}
