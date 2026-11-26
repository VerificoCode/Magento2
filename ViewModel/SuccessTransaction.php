<?php

namespace Verifico\Ageverify\ViewModel;

use Magento\Checkout\Model\Session;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Verifico\Ageverify\Model\Verify;

class SuccessTransaction implements ArgumentInterface
{
    public function __construct(
        private Session $checkoutSession,
        private Verify $verify
    ) {
    }

    public function getTransactionJson(): ?string
    {
        $order = $this->checkoutSession->getLastRealOrder();
        $transaction = $order && $order->getId() ? $this->verify->getTransaction($order) : null;

        return $transaction === null ? null : json_encode(
            $transaction,
            JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_THROW_ON_ERROR
        );
    }
}
