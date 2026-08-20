<?php
namespace Verifico\Ageverify\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

class Categorylist implements ArrayInterface
{
    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
	private $_storeManager;

	/**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
	private $_categoryFactory;

    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
	public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->_categoryFactory = $categoryFactory;
		$this->_storeManager = $storeManager;
    }

    public function toOptionArray()
    {
		$parent = $this->_storeManager->getStore()->getRootCategoryId();
        $resultArray = $this->getCategory($parent);
        return $resultArray;
    }

	private function getCategory($parent_id = '1')
	{
		$category = $this->_categoryFactory->create()->load($parent_id);
		$categories = array();
		$categories[] = array('value' => $category->getId(), 'label' => $category->getName());
		if ($category->hasChildren()) {
			$this->getCategoryTree($categories, '', $category->getId());
		}
		return $categories;
	}

	private function getCategoryTree(&$cattree_array, $spacing = '', $parent_id = 0)
	{
		if (!is_array($cattree_array)) {
			$cattree_array = array();
		}
		$category = $this->_categoryFactory->create()->load($parent_id);
		foreach ($category->getChildrenCategories() as $childCategory)
		{
			$cattree_array[] = array('value' => $childCategory->getId(), 'label' => $spacing.$childCategory->getName());
			if ($category->hasChildren()) {
			   $this->getCategoryTree(
			   $cattree_array,
			   $spacing.$childCategory->getName() . ' -> ',
			   $childCategory->getId()
			   );
			}
		}
    }
}
