<?php

namespace Meraki\Catalog\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AssignProductToParentCategory extends Command
{
    protected $_categories = [11, 20];
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    private $collectionFactory;
    /**
     * @var \Magento\Catalog\Api\CategoryLinkManagementInterface
     */
    private $categoryLinkManagement;

    public function __construct(
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $collectionFactory,
        \Magento\Catalog\Api\CategoryLinkManagementInterface $categoryLinkManagement,
        string $name = null
    )
    {
        parent::__construct($name);
        $this->collectionFactory = $collectionFactory;
        $this->categoryLinkManagement = $categoryLinkManagement;
    }

    protected function configure()
    {
        $this->setName('meraki:assign_products_to_parent_category')
            ->setDescription('Assign Products To Parent Category');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $categories = $this->collectionFactory->create();
        $categories->addAttributeToSelect('name');
        $categories->addFieldToFilter('entity_id', ['in' => $this->_categories]);
        /**
         * @var $category \Magento\Catalog\Model\Category
         */
        foreach ($categories as $category) {
            echo "---" . $category->getName() . "\n";
            $products = $this->_getAllChildProduct($category);
            echo "assigning " . sizeof($products) . "\n";
            /**
             * @var $product \Magento\Catalog\Model\Product
             */
            foreach ($products as $product) {
                $this->categoryLinkManagement->assignProductToCategories($product->getSku(), array_merge($product->getCategoryIds(), [$category->getId()]));
            }
        }
    }

    /**
     * @param $category  \Magento\Catalog\Model\Category
     * @return array
     */
    protected function _getAllChildProduct($category)
    {
        $result = [];

        foreach ($category->getProductCollection() as $product) {
            $result[] = $product;
        }

        foreach ($category->getChildrenCategories() as $child) {
            echo $child->getName() . "\n";
            $result = array_merge($result, $this->_getAllChildProduct($child));
        }

        return $result;
    }
}
