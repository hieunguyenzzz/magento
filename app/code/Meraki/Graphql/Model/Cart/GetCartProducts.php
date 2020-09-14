<?php
declare(strict_types=1);

namespace Meraki\Graphql\Model\Cart;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Quote\Model\Quote;

class GetCartProducts
{
    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * Get product models based on items in cart
     *
     * @param Quote $cart
     * @return ProductInterface[]
     */

    /**
     * @param ProductRepositoryInterface $productRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    )
    {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function execute(Quote $cart): array
    {
        $cartItems = [];
        foreach ($cart->getItemsCollection() as $item) {
            if (!$item->isDeleted() && $item->getProduct()->getTypeid() !== 'configurable') {
                $cartItems[] = $item;
            }
        }
        if (empty($cartItems)) {
            return [];
        }
        $cartItemIds = \array_map(
            function ($item) {
                return $item->getProduct()->getId();
            },
            $cartItems
        );

        $searchCriteria = $this->searchCriteriaBuilder->addFilter('entity_id', $cartItemIds, 'in')->create();
        $products = $this->productRepository->getList($searchCriteria)->getItems();

        return $products;
    }

}
