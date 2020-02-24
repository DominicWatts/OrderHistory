<?php

namespace Xigen\OrderHistory\Model\Data;

use Xigen\OrderHistory\Api\Data\HistoryItemInterface;

/**
 * Order history model history item api data class
 */
class HistoryItem extends \Magento\Framework\Api\AbstractExtensibleObject implements HistoryItemInterface
{

    /**
     * Get item_id
     * @return string|null
     */
    public function getItemId()
    {
        return $this->_get(self::ITEM_ID);
    }

    /**
     * Set item_id
     * @param string $itemId
     * @return \Xigen\OrderHistory\Api\Data\HistoryItemInterface
     */
    public function setItemId($itemId)
    {
        return $this->setData(self::ITEM_ID, $itemId);
    }

    /**
     * Get item
     * @return string|null
     */
    public function getItem()
    {
        return $this->_get(self::ITEM);
    }

    /**
     * Set item
     * @param string $item
     * @return \Xigen\OrderHistory\Api\Data\HistoryItemInterface
     */
    public function setItem($item)
    {
        return $this->setData(self::ITEM, $item);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Xigen\OrderHistory\Api\Data\HistoryItemExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Xigen\OrderHistory\Api\Data\HistoryItemExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Xigen\OrderHistory\Api\Data\HistoryItemExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
