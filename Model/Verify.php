<?php

namespace Verifico\Ageverify\Model;

use Verifico\Ageverify\Helper\Data as HelperData;
use Verifico\Ageverify\Block\Success;
use GuzzleHttp\Client;
use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ResponseFactory;
use Magento\Framework\Webapi\Rest\Request;
use Magento\Framework\Stdlib\DateTime;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Email\Sender\OrderSender as OrderSender;
use Verifico\Ageverify\Model\Config;

// use Magento\Integration\Model\IntegrationFactory;
// use Magento\Integration\Model\OauthService;
// use Magento\Integration\Model\AuthorizationService;
// use Magento\Integration\Model\Oauth\Token;
// use Verifico\Ageverify\Model\Config;

class Verify {

    protected $helperData;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @var DateTime\DateTime
     */
    protected $_date;

    protected $order;

    protected $_helperData;

    protected $_orderSender;

    protected $_transportBuilder;

    protected $_storeManager;

    protected $_logger;

    protected $authSession;

    protected $success;

    protected $unityConfig;

    public function __construct(
        HelperData $helperData,
        ClientFactory $clientFactory,
        ResponseFactory $responseFactory,
        DateTime\DateTime $date,
        Order $order,
        OrderSender $orderSender,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Backend\Model\Auth\Session $authSession,
        Success $success,
        \Verifico\Ageverify\Model\Config $unityConfig
        ) {
        $this->helperData = $helperData;
        $this->clientFactory = $clientFactory;
        $this->responseFactory = $responseFactory;
        $this->_date = $date;
        $this->order = $order;
        $this->_orderSender = $orderSender;
        $this->_transportBuilder = $transportBuilder;
        $this->_storeManager = $storeManager;
        $this->_logger = $logger;
        $this->authSession = $authSession;
        $this->success = $success;
        $this->unityConfig = $unityConfig;
    }

    public function shouldVerify($order) {

        $storeId = (int) $order->getStoreId();

        if (!$this->helperData->isEnabled($storeId)) {
            return 0;
        }

        // Get order country code
        $billingAddress = $order->getBillingAddress();
        $orderCountry = $billingAddress->getCountryId();

        if(is_array($this->helperData->getCountries($storeId))) {
            if(
                ($this->helperData->getCountries($storeId) && !in_array($orderCountry, $this->helperData->getCountries($storeId)))
                ) {
                return 0;
            }
        }
        

        // All customers
        if($this->helperData->getApiVerificoMode($storeId)==0) { 
            return 1;
        }

        // Specific Products
        if($this->helperData->getApiVerificoMode($storeId)==1) { 
                foreach($order->getAllItems() as $item) {
                    $product = $item->getProduct();
                    if($product->getAgeVerifiedVerifyProductYesNo()==1) {
                        // Should verify
                        return 1;
                    };
                }
        }

        // Specific Categories
        if($this->helperData->getApiVerificoMode($storeId)==2) { 
            $orderAllItems = $order->getAllItems();
            $selectedCategories = explode(',', $this->helperData->getSelectedCategory($storeId));

            if ($orderAllItems) {
                foreach ($orderAllItems as $item) {
                    $product = $item->getProduct();

                    $categoryIds = $product->getCategoryIds();
                    if (!empty(array_intersect($categoryIds, $selectedCategories))) {
                        // Should verify
                        return 1;
                    }



                }
            }
        }

    }

    public function ageVerifyOrder($order) {

        $acTransaction = [
            "user" => [
                "email" => $order->getCustomerEmail(),
                "firstname" => $billingAddress->getFirstname(),
                "lastname" => $billingAddress->getLastname(),
                "dob" => $order->getCustomerDob(),
                "order_id" => $order->getId(),
                "order_ref" => $order->getIncrementId(),
                "address" => [
                    "address1" => $billingAddress->getStreet()[0],
                    "address2" => "",
                    "address3" => "",
                    "city" => $billingAddress->getCity(),
                    "postcode" => $billingAddress->getPostcode(),
                    "county" => $billingAddress->getRegion(),
                    "country" => $billingAddress->getCountryId()
                ]
            ]
        ];

        // Add products
        $acTransaction["cart"] = $this->success->getAvOrderItems($order);

        $request = $acTransaction;

        $url = $this->unityConfig->getUnityIntegrationName()."/r?site_key=testkey";

        $response = $this->doRequest($endPointPath, $request, Request::HTTP_METHOD_POST);

    }

    private function doRequest(
        string $uriEndpoint,
        array $data = [],
        string $requestMethod = Request::HTTP_METHOD_POST
    ): Response {
        /** @var Client $client */
        // $apiConnection = $this->_helperData->getApiLoadAgeCheck();
        $client = $this->clientFactory->create(['config' => [
            'base_uri' => $apiConnection['request_uri']
        ]]);

        try {

            $response = $client->request(
                $requestMethod,
                $uriEndpoint,
                ['json' => $data]
            );
            
            
        } catch (GuzzleException $exception) {
            /** @var Response $response */
            $response = $this->responseFactory->create([
                'status' => $exception->getCode(),
                'reason' => $exception->getMessage()
            ]);
        }

        return $response;
    }

}