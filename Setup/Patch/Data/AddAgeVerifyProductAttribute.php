<?php

namespace Verifico\Ageverify\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Model\Entity\Attribute\Source\Boolean;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Integration\Model\ConfigBasedIntegrationManager;
use Verifico\Ageverify\Model\Config;

/**
 * Replaces the old Setup/InstallData.php. Stores that already ran it (module data
 * version 1.0.20 or later) skip this patch.
 */
class AddAgeVerifyProductAttribute implements DataPatchInterface, PatchVersionInterface
{
    public function __construct(
        private ModuleDataSetupInterface $setup,
        private EavSetupFactory $eavSetupFactory,
        private ConfigBasedIntegrationManager $integrationManager,
        private Config $unityConfig
    ) {
    }

    public function apply(): self
    {
        // Name must match etc/integration/config.xml
        $this->integrationManager->processIntegrationConfig([$this->unityConfig->getUnityIntegrationRef()]);

        $group = $this->unityConfig->getUnityName();
        // With 'group' set, the attribute is added to that group in every product attribute set
        $this->eavSetupFactory->create(['setup' => $this->setup])->addAttribute(
            Product::ENTITY,
            'age_verified_verify_product_yes_no',
            [
                'group' => $group,
                'type' => 'int',
                'label' => 'Age verify this product',
                'input' => 'boolean',
                'source' => Boolean::class,
                'global' => ScopedAttributeInterface::SCOPE_STORE,
                'visible' => true,
                'required' => false,
                'user_defined' => false,
                'default' => '0',
                'searchable' => false,
                'filterable' => false,
                'comparable' => false,
                'visible_on_front' => false,
                'used_in_product_listing' => false,
                'unique' => false,
                'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
                'sort_order' => 100,
                'note' => "This option only works when 'Verify Mode' is set to 'Specific Products' in the {$group} configuration.",
            ]
        );

        return $this;
    }

    public static function getVersion(): string
    {
        return '1.0.20';
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
