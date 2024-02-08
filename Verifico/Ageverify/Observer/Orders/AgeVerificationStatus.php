<?php
namespace Verifico\Ageverify\Observer\Orders;

use Magento\Framework\Event\ObserverInterface;

class AgeVerificationStatus implements ObserverInterface
{   
  public function __construct    (             
   \Magento\Sales\Model\Order $order,
   \Verifico\Ageverify\Model\Verify $verify
  ) 
{        
 $this->order = $order; 
 $this->verify = $verify;    
}

public function execute(\Magento\Framework\Event\Observer $observer)
 {   
  
  //   $orderId = $observer->getEvent()->getOrder()->getId();
  //   $order = $this->order->load($orderId); 

  $order = $observer->getEvent()->getOrder();

  $shouldVerify = $this->verify->shouldVerify($order);

  if($shouldVerify) {
    // Place on hold
    if($order->canHold()) {
      $order->hold(); 
    }
    
    $order->setUnityAgeVerificationStatus('Pending');
    $order->save();

  } else  {

    // If the order does not require age verification
    $order->setUnityAgeVerificationStatus('Age Verification Not Required');
    $order->save();

  }


  
 }
}