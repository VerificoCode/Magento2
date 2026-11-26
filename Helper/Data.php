<?php
namespace Verifico\Ageverify\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    public function isEnabled($storeId = null)
		{
				return $this->scopeConfig->isSetFlag(
						'verificounitysection/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $storeId
				);
		}

    public function getApiVerificoMode($storeId = null)
    {
		return $this->scopeConfig->getValue(
		'verificounitysection/general/only_flagged_products',
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
		$storeId
		);
    }

	public function getSelectedCategory($storeId = null)
    {
		return $this->scopeConfig->getValue(
		'verificounitysection/general/assign_category',
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
		$storeId
		);
    } 

    public function isHoldEnabled($storeId = null): bool
    {
		return $this->scopeConfig->isSetFlag(
		'verificounitysection/general/hold_orders',
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
		$storeId
		);
    }

    public function isCronEnabled($storeId = null): bool
    {
		return $this->scopeConfig->isSetFlag(
		'verificounitysection/general/cron_enable',
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
		$storeId
		);
    }

    public function getCronLookbackHours($storeId = null): int
    {
		return max(1, (int) $this->scopeConfig->getValue(
		'verificounitysection/general/cron_lookback_hours',
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
		$storeId
		));
    }

    public function getCountries($storeId = null)
    {
		return $this->scopeConfig->getValue(
		'verificounitysection/general/allowed_countries',
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
		$storeId
		);
    }
    
}