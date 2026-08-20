<?php
namespace Verifico\Ageverify\Observer\Orders;

use Magento\Framework\Event\ObserverInterface;

class AgeVerificationStatus implements ObserverInterface
{   
  /** @var \Magento\Sales\Model\Order */
  private $order;

  /** @var \Verifico\Ageverify\Model\Verify */
  private $verify;

  /** @var \Verifico\Ageverify\Helper\Data */
  private $helperData;

  public function __construct(
   \Magento\Sales\Model\Order $order,
   \Verifico\Ageverify\Model\Verify $verify,
   \Verifico\Ageverify\Helper\Data $helperData
  ) 
{        
 $this->order = $order; 
 $this->verify = $verify;
 $this->helperData = $helperData;
}

public function execute(\Magento\Framework\Event\Observer $observer)
 {   
  
  //   $orderId = $observer->getEvent()->getOrder()->getId();
  //   $order = $this->order->load($orderId); 

  $order = $observer->getEvent()->getOrder();

  if(!$order instanceof \Magento\Sales\Model\Order) {
    return;
  }

  if (!$this->helperData->isEnabled($order->getStoreId())) {
    $order->setUnityAgeVerificationStatus('Age Verification Disabled');
    return;
  }

  $shouldVerify = $this->verify->shouldVerify($order);

  if($shouldVerify) {
    // Place on hold
    if($order->canHold()) {
      $order->hold(); 
    }
    
    $order->setUnityAgeVerificationStatus('Pending');
    // $order->save();

  } else  {

    // If the order does not require age verification
    $order->setUnityAgeVerificationStatus('Age Verification Not Required');
    // $order->save();

  }


  
 }
}