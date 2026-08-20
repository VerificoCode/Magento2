<?php
namespace Verifico\Ageverify\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

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

	public function getApiVerificoEnable($storeId = null)
    {
		return (int) $this->isEnabled($storeId);
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

    public function getCountries($storeId = null)
    {
		return $this->scopeConfig->getValue(
		'verificounitysection/general/allowed_countries',
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE,
		$storeId
		);
    }
    
}