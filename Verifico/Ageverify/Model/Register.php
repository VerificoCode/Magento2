<?php

namespace Verifico\Ageverify\Model;

use Magento\Integration\Model\IntegrationFactory;
use Magento\Integration\Model\OauthService;
use Magento\Integration\Model\AuthorizationService;
use Magento\Integration\Model\Oauth\Token;
use Verifico\Ageverify\Model\Config;



class Register {

    protected $integrationFactory;
    protected $oauthService;
    protected $authorizationService;
    protected $token;
    protected $_storeManager;
    protected $scopeConfig;
    protected $_logo;
    protected $unityConfig;

    public function __construct(
        IntegrationFactory $integrationFactory,
        OauthService $oauthService,
        AuthorizationService $authorizationService,
        Token $token,   
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Verifico\Ageverify\Model\Config $unityConfig
        ) {
        $this->integrationFactory = $integrationFactory;
        $this->oauthService = $oauthService;
        $this->authorizationService = $authorizationService;
        $this->token = $token;
        $this->_storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->_logo = $logo;
        $this->unityConfig = $unityConfig;
      }

    // public function register() {

    //     // Check if already registered


    //     //Set your Data
    //     $name = $this->unityConfig->getUnityIntegrationName();
    //     $email = $this->unityConfig->getUnityIntegrationEmail();
    //     $endpoint = $this->unityConfig->getUnityIntegrationEndpoint(); //(e.g 'http://localhost/magento/')

    //     // Code to check whether the Integration is already present or not
    //     // $integrationExists = $this->integrationFactory->create()->load($name,'name')->getData();

    //     $stuff = $this->integrationFactory->create()->getCollection()->addFieldToFilter(
    //         'name', ['like' => '%'.$name.'%']
            
    //         );
        
    //     if($stuff->count()>0) {
    //         $name = $name.$stuff->count();
    //     }


    //     $integrationData = array(
    //         'name' => $name,
    //         'email' => $email,
    //         'status' => '1',
    //         'endpoint' => $endpoint,
    //         'setup_type' => '0'
    //     );
    //     try{
    //         // Code to create Integration
    //         $integrationFactory = $this->integrationFactory->create();
    //         $integration = $integrationFactory->setData($integrationData);
    //         $integration->save();
    //         $integrationId = $integration->getId();
    //         $consumerName = $name . $integrationId;


    //         // Code to create consumer
    //         $oauthService = $this->oauthService;
    //         $consumer = $oauthService->createConsumer(['name' => $consumerName]);
    //         $consumerId = $consumer->getId();
    //         $integration->setConsumerId($consumer->getId());
    //         $integration->save();
    //         // Code to grant permission
    //         $authorizeService = $this->authorizationService;
    //         $authorizeService->grantAllPermissions($integrationId);

    //         // Code to Activate and Authorize
    //         $token = $this->token;
    //         $uri = $token->createVerifierToken($consumerId);
    //         $token->setType('access');
    //         $token->save();

    //         return  $token->getToken();

    //     }catch(Exception $e){
    //         echo 'Error : '.$e->getMessage();
    //     }
    // }

   

    public function getSetupData() {
        return [
                "name"=>$this->_storeManager->getStore()->getName(),
                "url"=>$this->_storeManager->getStore()->getBaseUrl(),
                "email"=>$this->scopeConfig->getValue(
                    'trans_email/ident_sales/email', 
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                ),
                "logo"=>$this->_logo->getLogoSrc(),
                "platform"=>"magento",
                "access_token"=>""
            ];
        

    }
    
}