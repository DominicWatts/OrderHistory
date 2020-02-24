<?php

namespace Xigen\OrderHistory\Api\Data;

/**
 * Interface HistoryAddressInterface
 */
interface HistoryAddressInterface extends \Magento\Framework\Api\ExtensibleDataInterface
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
     * @return \Xigen\OrderHistory\Api\Data\HistoryAddressInterface
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
     * @return \Xigen\OrderHistory\Api\Data\HistoryAddressInterface
     */
    public function setItem($item);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Xigen\OrderHistory\Api\Data\HistoryAddressExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Xigen\OrderHistory\Api\Data\HistoryAddressExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Xigen\OrderHistory\Api\Data\HistoryAddressExtensionInterface $extensionAttributes
    );
}
