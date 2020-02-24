<?php

namespace Xigen\OrderHistory\Model;

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;
use Xigen\OrderHistory\Api\Data\HistoryInterfaceFactory;
use Xigen\OrderHistory\Api\Data\HistorySearchResultsInterfaceFactory;
use Xigen\OrderHistory\Api\HistoryRepositoryInterface;
use Xigen\OrderHistory\Model\ResourceModel\History as ResourceHistory;
use Xigen\OrderHistory\Model\ResourceModel\History\CollectionFactory as HistoryCollectionFactory;

/**
 * Order history model history repository class
 */
class HistoryRepository implements HistoryRepositoryInterface
{
    /**
     * @var ResourceHistory
     */
    protected $resource;

    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * @var HistoryCollectionFactory
     */
    protected $historyCollectionFactory;

    /**
     * @var HistorySearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var HistoryInterfaceFactory
     */
    protected $dataHistoryFactory;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @param ResourceHistory $resource
     * @param HistoryFactory $historyFactory
     * @param HistoryInterfaceFactory $dataHistoryFactory
     * @param HistoryCollectionFactory $historyCollectionFactory
     * @param HistorySearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceHistory $resource,
        HistoryFactory $historyFactory,
        HistoryInterfaceFactory $dataHistoryFactory,
        HistoryCollectionFactory $historyCollectionFactory,
        HistorySearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->historyFactory = $historyFactory;
        $this->historyCollectionFactory = $historyCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataHistoryFactory = $dataHistoryFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
        $this->collectionProcessor = $collectionProcessor;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        \Xigen\OrderHistory\Api\Data\HistoryInterface $history
    ) {
        /* if (empty($history->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $history->setStoreId($storeId);
        } */

        $historyData = $this->extensibleDataObjectConverter->toNestedArray(
            $history,
            [],
            \Xigen\OrderHistory\Api\Data\HistoryInterface::class
        );

        $historyModel = $this->historyFactory->create()->setData($historyData);

        try {
            $this->resource->save($historyModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the history: %1',
                $exception->getMessage()
            ));
        }
        return $historyModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($entityId)
    {
        $history = $this->historyFactory->create();
        $this->resource->load($history, $entityId);
        if (!$history->getId()) {
            throw new NoSuchEntityException(__('History with id "%1" does not exist.', $entityId));
        }
        return $history->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->historyCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Xigen\OrderHistory\Api\Data\HistoryInterface::class
        );

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $items[] = $model->getDataModel();
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        \Xigen\OrderHistory\Api\Data\HistoryInterface $history
    ) {
        try {
            $historyModel = $this->historyFactory->create();
            $this->resource->load($historyModel, $history->getHistoryId());
            $this->resource->delete($historyModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the History: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($entityId)
    {
        return $this->delete($this->get($entityId));
    }
}
