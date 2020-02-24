<?php

namespace Xigen\OrderHistory\Api\Data;

/**
 * Interface HistoryInterface
 */
interface HistoryInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ENTITY_ID = 'entity_id';
    const ITEM = 'item';

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId();

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Xigen\OrderHistory\Api\Data\HistoryInterface
     */
    public function setEntityId($entityId);

    /**
     * Get item
     * @return string|null
     */
    public function getItem();

    /**
     * Set item
     * @param string $item
     * @return \Xigen\OrderHistory\Api\Data\HistoryInterface
     */
    public function setItem($item);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Xigen\OrderHistory\Api\Data\HistoryExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Xigen\OrderHistory\Api\Data\HistoryExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Xigen\OrderHistory\Api\Data\HistoryExtensionInterface $extensionAttributes
    );
}
