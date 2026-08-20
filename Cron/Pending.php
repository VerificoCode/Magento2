<?php
namespace Verifico\Ageverify\Cron;

class Pending 
{

    public function execute()
    {   
        // Get all orders and filter by custom attribute - limit by date range to last x days
        $orders = $this->getOrders();

        // Loop through list and send to ACP
        foreach($orders as $order) {

            $order = $this->order->load($order['order_id']);

            // Push to ACP

        }
        
            
    }


    public function getOrders()
    {
        $to = date("Y-m-d H:i:s");
        $from = strtotime('-2 day', strtotime($to));
        $from = date('Y-m-d H:i:s', $from);
        // $from = '2021-04-27 00:00:00';
        // To get orders
        // Get orders from age_verified where there is no apprived status

        $collection = $this->_orderCollectionFactory->create();

        // $collection->addFieldToSelect('customer_email, increment_id, agechecked_ageverifiedid, entity_id as order_id');
        $collection->addFieldToSelect(['order_id' => 'entity_id', 'increment_id', 'unity_age_verification_status', 'customer_email']);
        $collection->addFieldToFilter('main_table.created_at', array('from'=>$from, 'to'=>$to));
        $collection->addFieldToFilter('main_table.unity_age_verification_status', ['eq' => 'Pending']);
        // $collection->addFieldToFilter('main_table.state', ['nin' => ["canceled","complete","closed","processing"]]);
        // $collection->columns(['entity_id' => 'order_id', 'customer_email' => 'customer_email']);

        return $collection;
    }

    
}