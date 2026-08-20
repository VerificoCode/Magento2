<?php
namespace Verifico\Ageverify\Block;

use Magento\Catalog\Model\Product;

class Success extends \Magento\Checkout\Block\Onepage\Success {


    private $productRepository; 
    private $categoryFactory;
    private $_helperData;
    private $customer;
    protected $customerRepository;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customer,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Order\Config $orderConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Verifico\Ageverify\Helper\Data $_helperData,
        array $data = []
    ) {
        parent::__construct($context, $checkoutSession, $orderConfig, $httpContext, $data);
        $this->productRepository = $productRepository;
        $this->customer = $customer;
        $this->customerRepository = $customerRepository;
        $this->categoryFactory = $categoryFactory;
        $this->_helperData = $_helperData;
    }

    public function getOrder() {
        return $this->_checkoutSession->getLastRealOrder();
    }

    public function getAvOrderItems($order = []) {
        
        if(empty($order)) {
            $lastOrder = $this->_checkoutSession->getLastRealOrder();
            $orderAllItems = $lastOrder->getAllItems();
            $storeId = $lastOrder->getStoreId();
        } else {
            $orderAllItems = $order->getAllItems();
            $storeId = $order->getStoreId();
        }
        
        return $this->processOrderItems($orderAllItems, $storeId);
        
    }

    public function processOrderItems($orderAllItems, $storeId = null) {
        $i = [];


        //Check verify mode and if selected products skip verify if nothing needs verification
        if($this->_helperData->getApiVerificoMode($storeId)==1) {

            $s=0;
            if ($orderAllItems) {
                
                foreach ($orderAllItems as $item) {
                    $verify = 0;
                    $product = $item->getProduct();
                    $verifyVal = $product->getAgeVerifiedVerifyProductYesNo();
                    $categoryIds = $product->getCategoryIds();
                    if($verifyVal==1) {
                        //Set flag to verify order
                        $verify = 1;
                    }
                    $i[$s]['getSku'] = $item->getSku();
                    $i[$s]['getName'] = str_replace('"','&quot;',$item->getName());
                    $i[$s]['getBaseRowTotalInclTax'] = $item->getBaseRowTotalInclTax();
                    $i[$s]['getQtyOrdered'] = $item->getQtyOrdered();
                    $i[$s]['requires_av'] = $verify;
                    
                    $s++;
                }
            }
        } else if($this->_helperData->getApiVerificoMode($storeId)==2) {
            

            //Get categories that require verification
            $selectedCategories = explode(',', $this->_helperData->getSelectedCategory($storeId));
            $s=0;
            if ($orderAllItems) {
                
                foreach ($orderAllItems as $item) {
                    $verify = 0;
                    $product = $item->getProduct();
                    $categoryIds = $product->getCategoryIds();
                    if (!empty(array_intersect($categoryIds, $selectedCategories))) {
                        //Set flag to verify order
                        $verify = 1;
                    }
                    $i[$s]['getSku'] = $item->getSku();
                    $i[$s]['getName'] = str_replace('"','&quot;',$item->getName());
                    $i[$s]['getBaseRowTotalInclTax'] = $item->getBaseRowTotalInclTax();
                    $i[$s]['getQtyOrdered'] = $item->getQtyOrdered();
                    $i[$s]['requires_av'] = $verify;
                    
                    $s++;
                }
            }

            
        } else {
            $s=0;
            if ($orderAllItems) {
                foreach ($orderAllItems as $item) {
                    $verify = 1;
                    $i[$s]['getSku'] = $item->getSku();
                    $i[$s]['getName'] = str_replace('"','&quot;',$item->getName());
                    $i[$s]['getBaseRowTotalInclTax'] = $item->getBaseRowTotalInclTax();
                    $i[$s]['getQtyOrdered'] = $item->getQtyOrdered();
                    $i[$s]['requires_av'] = $verify;
                    
                    $s++;
                }
            }
        }

        return $i;
    }

    public function getAVProduct($sku) {
        return $this->productRepository->get($sku);
        
    }

    public function getAVCategoryByUrlKey($sku) {
        $categories = $this->categoryFactory->create()->getCollection()
             ->addAttributeToFilter('url_key', $urlKey)
             ->addAttributeToSelect(['entity_id']);
        //return $categories;
    }

    public function getCustomer() {
        return $this->customer;
    }

    public function getCustomerAttributes() {
        $customerId  = $this->getCustomer()->getId();
        $customer = $this->customerRepository->getById($customerId);
        return $customer->getCustomAttributes();
    }

}