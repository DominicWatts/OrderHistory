<?php

namespace Xigen\OrderHistory\Model\Data;

use Xigen\OrderHistory\Api\Data\HistoryAddressInterface;

/**
 * Order history model history address api data class
 */
class HistoryAddress extends \Magento\Framework\Api\AbstractExtensibleObject implements HistoryAddressInterface
{

    /**
     * Get entity_id
     * @return string|null
     */
    public function getEntityId()
    {
        return $this->_get(self::ENTITY_ID);
    }

    /**
     * Set entity_id
     * @param string $entityId
     * @return \Xigen\OrderHistory\Api\Data\HistoryAddressInterface
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
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
     * @return \Xigen\OrderHistory\Api\Data\HistoryAddressInterface
     */
    public function setItem($item)
    {
        return $this->setData(self::ITEM, $item);
    }

    /**
     * Retrieve existing extension attributes object or create a new one.
     * @return \Xigen\OrderHistory\Api\Data\HistoryAddressExtensionInterface|null
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * Set an extension attributes object.
     * @param \Xigen\OrderHistory\Api\Data\HistoryAddressExtensionInterface $extensionAttributes
     * @return $this
     */
    public function setExtensionAttributes(
        \Xigen\OrderHistory\Api\Data\HistoryAddressExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }
}
