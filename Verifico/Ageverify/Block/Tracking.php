<?php
namespace Verifico\Ageverify\Block;

use \Magento\Framework\View\Element\Template\Context;
use Magento\Framework\View\Element\Template;

use Verifico\Ageverify\Model\Config;

class Tracking extends \Magento\Framework\View\Element\Template
{
    
    protected $unityConfig;

    public function __construct(
        Template\Context $context,
        \Verifico\Ageverify\Model\Config $unityConfig,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->unityConfig = $unityConfig;

    }

    public function getUnityUrl() {
        return $this->unityConfig->getUnityUrl();
    }

}