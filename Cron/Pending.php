<?php

namespace Verifico\Ageverify\Cron;

use Magento\Sales\Model\ResourceModel\Order\CollectionFactory;
use Magento\Store\Model\StoreManagerInterface;
use Verifico\Ageverify\Helper\Data as HelperData;
use Verifico\Ageverify\Model\Verify;

/**
 * Safety net for the success page block: pushes recent orders still in 'Pending'
 * age verification to AgeChecked Plus+ once, in case the frontend never sent them.
 */
class Pending
{
    /** Give the success page a chance to send the order first. */
    private const GRACE_SECONDS = 900;

    /** ponytail: oldest-first page per store per run; raise if a store outpaces 100 AV orders per 5 minutes */
    private const BATCH_SIZE = 100;

    /** Unpaid or finished orders: nothing to verify. A held order is judged by the state it was held from. */
    private const SKIP_STATES = ['pending_payment', 'payment_review', 'canceled', 'closed'];

    public function __construct(
        private StoreManagerInterface $storeManager,
        private CollectionFactory $orderCollectionFactory,
        private HelperData $helperData,
        private Verify $verify
    ) {
    }

    public function execute(): void
    {
        $now = time();

        foreach ($this->storeManager->getStores() as $store) {
            $storeId = (int) $store->getId();
            if (!$this->helperData->isEnabled($storeId) || !$this->helperData->isCronEnabled($storeId)) {
                continue;
            }

            $from = $now - $this->helperData->getCronLookbackHours($storeId) * 3600;
            // sales_order.created_at is stored in UTC
            $collection = $this->orderCollectionFactory->create()
                ->addFieldToFilter('store_id', $storeId)
                ->addFieldToFilter('unity_age_verification_status', 'Pending')
                ->addFieldToFilter('unity_age_verification_sent_at', ['null' => true])
                ->addFieldToFilter('state', ['nin' => self::SKIP_STATES])
                ->addFieldToFilter('created_at', [
                    'from' => gmdate('Y-m-d H:i:s', $from),
                    'to' => gmdate('Y-m-d H:i:s', $now - self::GRACE_SECONDS),
                ])
                ->setOrder('created_at', 'ASC')
                ->setPageSize(self::BATCH_SIZE);
            $collection->getSelect()->where(
                "NOT (main_table.state = 'holded' AND IFNULL(main_table.hold_before_state, '') IN (?))",
                self::SKIP_STATES
            );

            foreach ($collection as $order) {
                if ($this->verify->ageVerifyOrder($order)) {
                    // Direct update: a full order save would re-run order observers and plugins
                    $collection->getConnection()->update(
                        $collection->getMainTable(),
                        ['unity_age_verification_sent_at' => gmdate('Y-m-d H:i:s')],
                        ['entity_id = ?' => (int) $order->getId()]
                    );
                }
            }
        }
    }
}
