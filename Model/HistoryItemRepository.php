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
use Xigen\OrderHistory\Api\Data\HistoryItemInterfaceFactory;
use Xigen\OrderHistory\Api\Data\HistoryItemSearchResultsInterfaceFactory;
use Xigen\OrderHistory\Api\HistoryItemRepositoryInterface;
use Xigen\OrderHistory\Model\ResourceModel\HistoryItem as ResourceHistoryItem;
use Xigen\OrderHistory\Model\ResourceModel\HistoryItem\CollectionFactory as HistoryItemCollectionFactory;

/**
 * Order history model history item repository class
 */
class HistoryItemRepository implements HistoryItemRepositoryInterface
{
    /**
     * @var ResourceHistoryItem
     */
    protected $resource;

    /**
     * @var HistoryItemFactory
     */
    protected $historyItemFactory;

    /**
     * @var HistoryItemCollectionFactory
     */
    protected $historyItemCollectionFactory;

    /**
     * @var HistoryItemSearchResultsInterfaceFactory
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
     * @var HistoryItemInterfaceFactory
     */
    protected $dataHistoryItemFactory;

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
     * @param ResourceHistoryItem $resource
     * @param HistoryItemFactory $historyItemFactory
     * @param HistoryItemInterfaceFactory $dataHistoryItemFactory
     * @param HistoryItemCollectionFactory $historyItemCollectionFactory
     * @param HistoryItemSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceHistoryItem $resource,
        HistoryItemFactory $historyItemFactory,
        HistoryItemInterfaceFactory $dataHistoryItemFactory,
        HistoryItemCollectionFactory $historyItemCollectionFactory,
        HistoryItemSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->historyItemFactory = $historyItemFactory;
        $this->historyItemCollectionFactory = $historyItemCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataHistoryItemFactory = $dataHistoryItemFactory;
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
        \Xigen\OrderHistory\Api\Data\HistoryItemInterface $historyItem
    ) {
        $historyItemData = $this->extensibleDataObjectConverter->toNestedArray(
            $historyItem,
            [],
            \Xigen\OrderHistory\Api\Data\HistoryItemInterface::class
        );

        $historyItemModel = $this->historyItemFactory->create()->setData($historyItemData);

        try {
            $this->resource->save($historyItemModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the historyItem: %1',
                $exception->getMessage()
            ));
        }
        return $historyItemModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($historyItemId)
    {
        $historyItem = $this->historyItemFactory->create();
        $this->resource->load($historyItem, $historyItemId);
        if (!$historyItem->getId()) {
            throw new NoSuchEntityException(__('HistoryItem with id "%1" does not exist.', $historyItemId));
        }
        return $historyItem->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->historyItemCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Xigen\OrderHistory\Api\Data\HistoryItemInterface::class
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
        \Xigen\OrderHistory\Api\Data\HistoryItemInterface $historyItem
    ) {
        try {
            $historyItemModel = $this->historyItemFactory->create();
            $this->resource->load($historyItemModel, $historyItem->getItemId());
            $this->resource->delete($historyItemModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the HistoryItem: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($historyItemId)
    {
        return $this->delete($this->get($historyItemId));
    }
}
