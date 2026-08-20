<?php 

namespace Verifico\Ageverify\Observer\Orders;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SyncOrderGrid implements ObserverInterface
{
    protected $connection;
    protected $resource;

    public function __construct(ResourceConnection $resource)
    {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();

        if (!$order || !$order->getId()) {
            return;
        }

        // Fields you want to sync from sales_order to sales_order_grid
        $data = [
            'unity_age_verification_status' => $order->getData('unity_age_verification_status'),
            // Add more fields if needed
        ];

        // Update the grid table
        $this->connection->update(
            $this->resource->getTableName('sales_order_grid'),
            $data,
            ['entity_id = ?' => (int) $order->getId()]
        );
    }
}