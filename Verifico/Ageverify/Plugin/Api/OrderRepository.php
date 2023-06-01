<?php

namespace Verifico\Ageverify\Plugin\Api;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class OrderRepository
 */
class OrderRepository 
{

    const UNITY_AGE_VERIFICATION_STATUS= 'unity_age_verification_status';

    /**
     * Order Extension Attributes Factory
     *
     * @var OrderExtensionFactory
     */
    protected $extensionFactory;

    /**
     * OrderRepositoryPlugin constructor
     *
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(OrderExtensionFactory $extensionFactory)
    {
       
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * Add "unity_age_verification_status" extension attribute to order data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(OrderRepositoryInterface $subject, OrderInterface $order)
    {
        // $customSalesId = $order->getData(self::UNITY_AGE_VERIFICATION_STATUS);
        $unityAgeVerificationStatus = $order->getData(self::UNITY_AGE_VERIFICATION_STATUS);
        $extensionAttributes = $order->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
        $extensionAttributes->setUnityAgeVerificationStatus($unityAgeVerificationStatus );
        $order->setExtensionAttributes($extensionAttributes);

        return $order;
    }

    /**
     * Add "unity_age_verification_status" extension attribute to order data object to make it accessible in API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResult
     *
     * @return OrderSearchResultInterface
     */
    public function afterGetList(OrderRepositoryInterface $subject, OrderSearchResultInterface $searchResult)
    {
        $orders = $searchResult->getItems();

        foreach ($orders as &$order) {
            $unityAgeVerificationStatus = $order->getData(self::UNITY_AGE_VERIFICATION_STATUS);
            $extensionAttributes = $order->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
            $extensionAttributes->setUnityAgeVerificationStatus($unityAgeVerificationStatus );
            $order->setExtensionAttributes($extensionAttributes);
        }

        return $searchResult;
    }

    public function beforeSave(OrderRepositoryInterface $subject, OrderInterface $order) {
        $extensionAttributes = $order->getExtensionAttributes() ?: $this->extensionFactory->create();
            if ($extensionAttributes !== null && $extensionAttributes->getUnityAgeVerificationStatus() !== null) {
                $order->setUnityAgeVerificationStatus($extensionAttributes->getUnityAgeVerificationStatus());
            }
        
            return [$order];
    }

}