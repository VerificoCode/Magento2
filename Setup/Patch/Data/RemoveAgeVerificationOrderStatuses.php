<?php

namespace Verifico\Ageverify\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Sales\Model\Order\Config as OrderConfig;

/**
 * Removes the custom order statuses older versions installed; the
 * unity_age_verification_status order field replaces them.
 */
class RemoveAgeVerificationOrderStatuses implements DataPatchInterface
{
    private const STATUSES = [
        'av_pending',
        'av_pending_new',
        'av_pending_processing',
        'av_low_risk',
        'av_high_risk',
    ];

    public function __construct(
        private ModuleDataSetupInterface $setup,
        private OrderConfig $orderConfig
    ) {
    }

    public function apply(): self
    {
        $connection = $this->setup->getConnection();
        $orderTable = $this->setup->getTable('sales_order');
        $gridTable = $this->setup->getTable('sales_order_grid');

        // Status/state assignments and store labels go with it (FK cascade)
        $connection->delete($this->setup->getTable('sales_order_status'), ['status IN (?)' => self::STATUSES]);

        // Move any orders still on these statuses to Magento's default status for their state
        $orderStates = $connection->fetchPairs(
            $connection->select()->from($orderTable, ['entity_id', 'state'])->where('status IN (?)', self::STATUSES)
        );
        foreach (array_unique($orderStates) as $state) {
            $status = $this->orderConfig->getStateDefaultStatus($state);
            if (!$status) {
                continue;
            }
            $ids = array_keys($orderStates, $state, true);
            foreach ([$orderTable, $gridTable] as $table) {
                $connection->update($table, ['status' => $status], ['entity_id IN (?)' => $ids]);
            }
        }

        return $this;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }
}
