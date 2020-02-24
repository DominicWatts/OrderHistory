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
use Xigen\OrderHistory\Api\Data\HistoryAddressInterfaceFactory;
use Xigen\OrderHistory\Api\Data\HistoryAddressSearchResultsInterfaceFactory;
use Xigen\OrderHistory\Api\HistoryAddressRepositoryInterface;
use Xigen\OrderHistory\Model\ResourceModel\HistoryAddress as ResourceHistoryAddress;
use Xigen\OrderHistory\Model\ResourceModel\HistoryAddress\CollectionFactory as HistoryAddressCollectionFactory;

/**
 * Order history model history address repository class
 */
class HistoryAddressRepository implements HistoryAddressRepositoryInterface
{
    /**
     * @var ResourceHistoryAddress
     */
    protected $resource;

    /**
     * @var HistoryAddressFactory
     */
    protected $historyAddressFactory;

    /**
     * @var HistoryAddressCollectionFactory
     */
    protected $historyAddressCollectionFactory;

    /**
     * @var HistoryAddressSearchResultsInterfaceFactory
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
     * @var HistoryAddressInterfaceFactory
     */
    protected $dataHistoryAddressFactory;

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
     * @param ResourceHistoryAddress $resource
     * @param HistoryAddressFactory $historyAddressFactory
     * @param HistoryAddressInterfaceFactory $dataHistoryAddressFactory
     * @param HistoryAddressCollectionFactory $historyAddressCollectionFactory
     * @param HistoryAddressSearchResultsInterfaceFactory $searchResultsFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param DataObjectProcessor $dataObjectProcessor
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     */
    public function __construct(
        ResourceHistoryAddress $resource,
        HistoryAddressFactory $historyAddressFactory,
        HistoryAddressInterfaceFactory $dataHistoryAddressFactory,
        HistoryAddressCollectionFactory $historyAddressCollectionFactory,
        HistoryAddressSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor,
        JoinProcessorInterface $extensionAttributesJoinProcessor,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter
    ) {
        $this->resource = $resource;
        $this->historyAddressFactory = $historyAddressFactory;
        $this->historyAddressCollectionFactory = $historyAddressCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataHistoryAddressFactory = $dataHistoryAddressFactory;
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
        \Xigen\OrderHistory\Api\Data\HistoryAddressInterface $historyAddress
    ) {
        $historyAddressData = $this->extensibleDataObjectConverter->toNestedArray(
            $historyAddress,
            [],
            \Xigen\OrderHistory\Api\Data\HistoryAddressInterface::class
        );

        $historyAddressModel = $this->historyAddressFactory->create()->setData($historyAddressData);

        try {
            $this->resource->save($historyAddressModel);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the historyAddress: %1',
                $exception->getMessage()
            ));
        }
        return $historyAddressModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function get($historyAddressId)
    {
        $historyAddress = $this->historyAddressFactory->create();
        $this->resource->load($historyAddress, $historyAddressId);
        if (!$historyAddress->getId()) {
            throw new NoSuchEntityException(__('HistoryAddress with id "%1" does not exist.', $historyAddressId));
        }
        return $historyAddress->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->historyAddressCollectionFactory->create();

        $this->extensionAttributesJoinProcessor->process(
            $collection,
            \Xigen\OrderHistory\Api\Data\HistoryAddressInterface::class
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
        \Xigen\OrderHistory\Api\Data\HistoryAddressInterface $historyAddress
    ) {
        try {
            $historyAddressModel = $this->historyAddressFactory->create();
            $this->resource->load($historyAddressModel, $historyAddress->getEntityId());
            $this->resource->delete($historyAddressModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the HistoryAddress: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($historyAddressId)
    {
        return $this->delete($this->get($historyAddressId));
    }
}
