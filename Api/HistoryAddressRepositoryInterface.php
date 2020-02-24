<?php

namespace Xigen\OrderHistory\Api;

/**
 * Interface HistoryAddressRepositoryInterface
 */
interface HistoryAddressRepositoryInterface
{

    /**
     * Save HistoryAddress
     * @param \Xigen\OrderHistory\Api\Data\HistoryAddressInterface $historyAddress
     * @return \Xigen\OrderHistory\Api\Data\HistoryAddressInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(
        \Xigen\OrderHistory\Api\Data\HistoryAddressInterface $historyAddress
    );

    /**
     * Retrieve HistoryAddress
     * @param string $entityId
     * @return \Xigen\OrderHistory\Api\Data\HistoryAddressInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function get($entityId);

    /**
     * Retrieve HistoryAddress matching the specified criteria.
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Xigen\OrderHistory\Api\Data\HistoryAddressSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    /**
     * Delete HistoryAddress
     * @param \Xigen\OrderHistory\Api\Data\HistoryAddressInterface $historyAddress
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(
        \Xigen\OrderHistory\Api\Data\HistoryAddressInterface $historyAddress
    );

    /**
     * Delete HistoryAddress by ID
     * @param string $entityId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($entityId);
}
