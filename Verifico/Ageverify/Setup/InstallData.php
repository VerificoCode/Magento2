<?php

namespace Verifico\Ageverify\Setup;


//use Magento\Cms\Model\BlockFactory;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Catalog\Setup\CategorySetupFactory;
use Magento\Catalog\Model\Product\AttributeSet\Options;
use Magento\Sales\Setup\SalesSetup;
use Magento\Cms\Model\BlockFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Status;
use Magento\Sales\Model\Order\StatusFactory;
use Magento\Sales\Model\ResourceModel\Order\Status as StatusResource;
use Magento\Sales\Model\ResourceModel\Order\StatusFactory as StatusResourceFactory;

use Magento\Integration\Model\ConfigBasedIntegrationManager;

use Verifico\Ageverify\Model\Config;



class InstallData implements InstallDataInterface
 {

    /**
    * @var ConfigBasedIntegrationManager
    */

    private $integrationManager;

    private $blockFactory;

    private $eavSetupFactory;

    private $categorySetupFactory;

    private $categoryAttIds;

    protected $statusFactory;

    protected $statusResourceFactory;

    protected $unityConfig;

    public function __construct(
        EavSetupFactory $eavSetupFactory,
        CategorySetupFactory $categorySetupFactory,
        Options $categoryAttIds,
        BlockFactory $blockFactory,
        StatusFactory $statusFactory,
        StatusResourceFactory $statusResourceFactory,
        ConfigBasedIntegrationManager $integrationManager,
        \Verifico\Ageverify\Model\Config $unityConfig
    )
    {
        
        $this->eavSetupFactory = $eavSetupFactory;
        $this->categorySetupFactory = $categorySetupFactory;
        $this->categoryAttIds = $categoryAttIds;
        $this->blockFactory = $blockFactory;
        $this->statusFactory = $statusFactory;
        $this->statusResourceFactory = $statusResourceFactory;
        $this->integrationManager = $integrationManager;
        $this->unityConfig = $unityConfig;
    }

/**
 * {@inheritdoc}
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
{
    
    $this->integrationManager->processIntegrationConfig([$this->unityConfig->getUnityIntegrationRef()]);
    // Needs to be the same as that in etc/integration/api.xml integration > name
    /**
     * Install order statuses from config
     */
    $data = [];
    //New order status
    $statuses = [
        'av_pending' => __('Pending Age Verification'),
        'av_low_risk' => __('Age Verified Low Risk'),
        'av_high_risk' =>__('Age Verified High Risk')
    ];

    $this->addNewOrderProcessingStatus('av_pending_new', 'Pending Age Verification', 'new');
    $this->addNewOrderProcessingStatus('av_pending_processing', 'Pending Age Verification', 'processing');
    $this->addNewOrderProcessingStatus('av_pending', 'Pending Age Verification', 'pending_payment');

    $this->addNewOrderProcessingStatus('av_low_risk', 'Age Verified Low Risk', 'new');
    $this->addNewOrderProcessingStatus('av_low_risk', 'Age Verified Low Risk', 'processing');
    $this->addNewOrderProcessingStatus('av_low_risk', 'Age Verified Low Risk', 'pending_payment');

    $this->addNewOrderProcessingStatus('av_high_risk', 'Age Verified High Risk', 'new');
    $this->addNewOrderProcessingStatus('av_high_risk', 'Age Verified High Risk', 'processing');
    $this->addNewOrderProcessingStatus('av_high_risk', 'Age Verified High Risk', 'pending_payment');


    foreach ($statuses as $code => $info) {
        $data[] = ['status' => $code, 'label' => $info];
    }
    //create status
    try {
        // $setup->getConnection()->insertArray($setup->getTable('sales_order_status'), ['status', 'label'], $data);
    } catch(\Exception $e) {}

    $data = [];

    //prepare data for associate status to state
    foreach ($statuses as $code => $label) {

        $item = [
            'label' => __($label),
            'statuses' => [$code => ['default' => '1'], $code => []],
            'visible_on_front' => true];
        $states[$code] = $item;
    }



    foreach ($states as $code => $info) {
        if (isset($info['statuses'])) {
            foreach ($info['statuses'] as $status => $statusInfo) {
                $data[] = [
                    'status' => $status,
                    'state' => 'processing', //state to associate
                    'is_default' => is_array($statusInfo) && isset($statusInfo['default']) ? 1 : 0,
                ];
            }
            foreach ($info['statuses'] as $status => $statusInfo) {
                $data[] = [
                    'status' => $status,
                    'state' => 'new', //state to associate
                    'is_default' => is_array($statusInfo) && isset($statusInfo['default']) ? 1 : 0,
                ];
            }
            foreach ($info['statuses'] as $status => $statusInfo) {
                $data[] = [
                    'status' => $status,
                    'state' => 'pending_payment', //state to associate
                    'is_default' => is_array($statusInfo) && isset($statusInfo['default']) ? 1 : 0,
                ];
            }
            $data[] = [
                'status' => 'Pending Age Verification',
                'state' => 'holded', //state to associate
                'is_default' => 0,
            ];
        }
    }



    // //Insert row for associate
    // try {
        
    //     $setup->getConnection()->insertArray(
    //     $setup->getTable('sales_order_status_state'),
    //     ['status', 'state', 'is_default'],
    //     $data
    // );
    // } catch(\Exception $e) {
    //     echo 'NOOOOOOO';
    // }

    // /** Update visibility for states */
    // $states = ['new', 'processing', 'complete', 'closed', 'canceled', 'holded', 'payment_review'];
    // foreach ($states as $state) {
    //     $setup->getConnection()->update(
    //         $setup->getTable('sales_order_status_state'),
    //         ['visible_on_front' => 1],
    //         ['state = ?' => $state]
    //     );
    // }


    $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        


        $categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
        
        $attributeSetId = $categorySetup->getDefaultAttributeSetId(\Magento\Catalog\Model\Product::ENTITY);
        //$attributeSetIds = $categorySetup->getAttributeSetIds(\Magento\Catalog\Model\Product::ENTITY);
        $attributeGroupName = $this->unityConfig->getUnityName();


        $defaultData = [
            'global'                        => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
            'backend'                       => '',
            'visible'                       => true,
            'searchable'                    => false,
            'filterable'                    => false,
            'comparable'                    => false,
            'is_configurable'               => false,
            'visible_on_front'              => false,
            'visible_in_advanced_search'    => false,
            'used_in_product_listing'       => false,
            'user_defined'                  => false,
            'required'                      => true,
        ];

        $attributesDataMap = [
            $attributeGroupName => [
                'sort_order' => 80,
                'default' => $defaultData,
                'attributes' => [
                    'age_verified_verify_product_yes_no' => [
                        'type' => 'int',
                        'backend' => '',
                        'frontend' => '',
                        'label' => 'Age verify this product',
                        'input' => 'boolean',
                        'class' => '',
                        'source' => \Magento\Eav\Model\Entity\Attribute\Source\Boolean::class,
                        'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                        'visible' => true,
                        'required' => false,
                        'user_defined' => false,
                        'default' => '1',
                        'searchable' => false,
                        'filterable' => false,
                        'comparable' => false,
                        'visible_on_front' => false,
                        'used_in_product_listing' => false,
                        'unique' => false,
                        'apply_to' => 'simple,configurable,virtual,bundle,downloadable',
                        'sort_order' => 100,
                        'note' => 'This option only works when \'Verify Mode\' is set to \'Specific Products\' in the '.$attributeGroupName.' configuration.'
                    ]
                ]
             ]
        ];
        

        $entity = \Magento\Catalog\Model\Product::ENTITY;
        $entityTypeId = $eavSetup->getEntityTypeId($entity);
        //foreach ($this->categorySetupFactory->getAllAttributeSetIds($entityTypeId) as $attributeSetId) {
        foreach ($this->categoryAttIds->toOptionArray() as $attributeSetId) {

            $attributeSetId = $attributeSetId['value'];
            
            foreach ($attributesDataMap as $groupName => $groupData) {
                # add groups to all sets OR change position
                $categorySetup->addAttributeGroup(
                    $entityTypeId,
                    $attributeSetId,
                    $groupName,
                    $groupData['sort_order']
                );

                if (count($groupData['attributes'])) {
                    foreach ($groupData['attributes'] as $attributeCode => $attributeData) {
                        $attributeId = $eavSetup->getAttributeId($entityTypeId, $attributeCode);
                        $attributeData = array_merge(
                            ['group' => $groupName],
                            $defaultData,
                            $groupData['default'],
                            $attributeData
                        );
                        # add new attribute
                        if (!$attributeId) {
                            $eavSetup->addAttribute(
                                $entityTypeId,
                                $attributeCode,
                                $attributeData
                            );
                        # update attribute psotion and group
                        } else {
                            $eavSetup->updateAttribute(
                                $entityTypeId,
                                $attributeCode,
                                $attributeData
                            );
                            $eavSetup->addAttributeToSet(
                                $entityTypeId,
                                $attributeSetId,
                                $groupName,
                                $attributeId,
                                $attributeData['sort_order']
                            );
                        }
                    }
                }
            }
        }


  }

  

  protected function addNewOrderProcessingStatus($statusCode, $label, $state) {

    /** @var StatusResource $statusResource */
    $statusResource = $this->statusResourceFactory->create();
    /** @var Status $status */
    $status = $this->statusFactory->create();
    $status->setData([
        'status' => $statusCode,
        'label' => $label,
    ]);
    try {
        $statusResource->save($status);
    } catch (AlreadyExistsException $exception) {
        return;
    }
    $status->assignState($state, false, true);
  }

  
}