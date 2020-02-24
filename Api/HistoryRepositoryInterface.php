<?php

namespace Xigen\OrderHistory\Api;

/**
 * Interface HistoryRepositoryInterface
 */
interface HistoryRepositoryInterface
{

    /**
     * Save History
     * @param \Xigen\OrderHistory\Api\Data\HistoryInterface $history
     * @return \Xigen\OrderHistory\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Xigen\OrderHistory\Api\Data\HistoryInterface $history
    );

    /**
     * Retrieve History
     * @param string $entityId
     * @return \Xigen\OrderHistory\Api\Data\HistoryInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($entityId);

    /**
     * Retrieve History matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Xigen\OrderHistory\Api\Data\HistorySearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete History
     * @param \Xigen\OrderHistory\Api\Data\HistoryInterface $history
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Xigen\OrderHistory\Api\Data\HistoryInterface $history
    );

    /**
     * Delete History by ID
     * @param string $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($entityId);
}
