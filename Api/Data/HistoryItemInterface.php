<?php

namespace Xigen\OrderHistory\Api\Data;

/**
 * Interface HistoryItemInterface
 */
interface HistoryItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    const ITEM_ID = 'item_id';
    const ITEM = 'item';

    /**
     * Get item_id
     * @return string|null
     */
    public function getItemId();

    /**
     * Set item_id
     * @param string $itemId
     * @return \Xigen\OrderHistory\Api\Data\HistoryItemInterface
     */
    public function setItemId($itemId);

    /**
     * Get item
     * @return string|null
     */
    public function getItem();

    /**
     * Set item
     * @param string $item
     * @return \Xigen\OrderHistory\Api\Data\HistoryItemInterface
     */
    public function setItem($item);

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Xigen\OrderHistory\Api\Data\HistoryItemExtensionInterface|null
     */
    public function getExtensionAttributes();

    /**
     * Set an extension attributes object.
     * @param \Xigen\OrderHistory\Api\Data\HistoryItemExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Xigen\OrderHistory\Api\Data\HistoryItemExtensionInterface $extensionAttributes
    );
}
