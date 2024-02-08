<?php
namespace Agecheck\Ageverification\Setup;

use Magento\Catalog\Model\Product\AttributeSet\Options;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

use Magento\Integration\Model\ConfigBasedIntegrationManager;
use Verifico\Ageverify\Model\Config;

class UpgradeData implements UpgradeDataInterface
{

    private $integrationManager;

    private $unityConfig;

    public function __construct(
        ConfigBasedIntegrationManager $integrationManager,
        \Verifico\Ageverify\Model\Config $unityConfig
    )
    {
        
        $this->integrationManager = $integrationManager;
        $this->unityConfig = $unityConfig;
    }

    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        
        $this->integrationManager->processIntegrationConfig([$this->unityConfig->getUnityIntegrationRef()]);

        $setup->endSetup();
    }
}