<?php

namespace Meraki\Graphql\Plugin\Model;

class QuotePlugin
{
    /**
     * showing child product in the cart instead of parent item
     *
     * @param \Magento\Quote\Model\Quote $subject
     * @param callable $proceed
     * @return array
     */
    public function aroundGetAllVisibleItems(\Magento\Quote\Model\Quote $subject, callable $proceed)
    {
        $items = [];
        foreach ($subject->getItemsCollection() as $item) {
            if (!$item->isDeleted() &&  $item->getProduct()->getTypeId() !== 'configurable') {
                $items[] = $item;
            }
        }
        return $items;
    }
}
