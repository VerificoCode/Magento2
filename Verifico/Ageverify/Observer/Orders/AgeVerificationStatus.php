<?php
namespace Verifico\Ageverify\Observer\Orders;

use Magento\Framework\Event\ObserverInterface;

class AgeVerificationStatus implements ObserverInterface
{   
  public function __construct    (             
   \Magento\Sales\Model\Order $order   
  ) 
{        
 $this->order = $order;     
}

public function execute(\Magento\Framework\Event\Observer $observer)
 {   
//   $orderId = $observer->getEvent()->getOrder()->getId();
//   $order = $this->order->load($orderId); 

  $order = $observer->getEvent()->getOrder();
  $order->setUnityAgeVerificationStatus('Pending');
  $order->save();
 }
}