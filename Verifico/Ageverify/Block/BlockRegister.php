<?php
namespace Verifico\Ageverify\Block;

use Verifico\Ageverify\Model\Register;
use Verifico\Ageverify\Model\Config;

class BlockRegister extends \Magento\Backend\Block\Template
{
    protected $register;
    protected $unityConfig;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Verifico\Ageverify\Model\Register $register, \Verifico\Ageverify\Model\Config $unityConfig) {
        parent::__construct($context);
        $this->register = $register;
        $this->unityConfig = $unityConfig;
    }

    public function getRegisterData() {
        return $this->register->getSetupData();
    }

    public function getUnityRegisterUrl() {
        return $this->unityConfig->getUnityRegisterUrl();
    }

}