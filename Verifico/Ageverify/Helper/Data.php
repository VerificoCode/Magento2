<?php
namespace Verifico\Ageverify\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

	public function getApiVerificoEnable()
    {
		return $this->scopeConfig->getValue(
		'verificoverify/general/enable',
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
    }

    public function getApiVerificoMode()
    {
		return $this->scopeConfig->getValue(
		'verificounity/general/only_flagged_products',
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
    }

	public function getSelectedCategory()
    {
		return $this->scopeConfig->getValue(
		'verificounity/general/only_flagged_products',
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
    } 

    public function getEnabledCountries()
    {	
		return $this->scopeConfig->getValue(
		'verificounity/general/only_flagged_products',
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
    }

    public function getCountries()
    {
		return $this->scopeConfig->getValue(
		'verificounity/general/only_flagged_products',
		\Magento\Store\Model\ScopeInterface::SCOPE_STORE
		);
    }
    
}