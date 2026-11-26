<?php

namespace Verifico\Ageverify\Observer\Orders;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;
use Verifico\Ageverify\Helper\Data as HelperData;
use Verifico\Ageverify\Model\Verify;

/**
 * Sets the Age Verification Status on a new order, and holds it when "Hold Orders Awaiting
 * Age Verification" is on.
 */
class AgeVerificationStatus implements ObserverInterface
{
    public function __construct(
        private Verify $verify,
        private HelperData $helperData
    ) {
    }

    public function execute(Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        if (!$order instanceof Order) {
            return;
        }

        if (!$this->helperData->isEnabled($order->getStoreId())) {
            $order->setUnityAgeVerificationStatus('Age Verification Disabled');
            return;
        }

        if (!$this->verify->shouldVerify($order)) {
            $order->setUnityAgeVerificationStatus('Age Verification Not Required');
            return;
        }

        $order->setUnityAgeVerificationStatus('Pending');

        // Never hold an order still waiting for payment: a held order cannot be invoiced, so the
        // payment callback would fail. ponytail: those orders are not held once paid either;
        // hold on the later state change if stores need it.
        if ($this->helperData->isHoldEnabled($order->getStoreId())
            && $order->getState() !== Order::STATE_PENDING_PAYMENT
            && $order->canHold()
        ) {
            $order->hold();
        }
    }
}
