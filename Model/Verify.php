<?php

namespace Verifico\Ageverify\Model;

use GuzzleHttp\ClientFactory;
use GuzzleHttp\Exception\GuzzleException;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;
use Verifico\Ageverify\Helper\Data as HelperData;

class Verify
{
    public function __construct(
        private HelperData $helperData,
        private ClientFactory $clientFactory,
        private LoggerInterface $logger,
        private Config $unityConfig
    ) {
    }

    public function shouldVerify($order)
    {
        $storeId = (int) $order->getStoreId();

        if (!$this->helperData->isEnabled($storeId)) {
            return 0;
        }

        // Config stores the multiselect as "GB,US"; empty means every country
        $countries = array_filter(explode(',', (string) $this->helperData->getCountries($storeId)));
        if ($countries && !in_array($order->getBillingAddress()?->getCountryId(), $countries, true)) {
            return 0;
        }

        $mode = (int) $this->helperData->getApiVerificoMode($storeId);

        // All customers
        if ($mode === 0) {
            return 1;
        }

        $selectedCategories = explode(',', (string) $this->helperData->getSelectedCategory($storeId));
        foreach ($order->getAllItems() as $item) {
            // Product is null when it has been deleted since it was added to the cart
            $product = $item->getProduct();
            if (!$product) {
                continue;
            }
            // Specific Products
            if ($mode === 1 && $product->getAgeVerifiedVerifyProductYesNo() == 1) {
                return 1;
            }
            // Specific Categories
            if ($mode === 2 && array_intersect((array) $product->getCategoryIds(), $selectedCategories)) {
                return 1;
            }
        }

        return 0;
    }

    /**
     * The order as AgeChecked Plus+ expects it, used by the success page script and the cron.
     */
    public function getTransaction(Order $order): ?array
    {
        $billingAddress = $order->getBillingAddress();
        if (!$billingAddress) {
            return null;
        }

        $storeId = $order->getStoreId();
        $mode = (int) $this->helperData->getApiVerificoMode($storeId);
        $selectedCategories = explode(',', (string) $this->helperData->getSelectedCategory($storeId));
        $products = [];

        foreach ($order->getAllItems() as $item) {
            $product = $item->getProduct();
            $products[] = [
                'id' => $item->getSku(),
                'name' => $item->getName(),
                'price' => (string) $item->getBaseRowTotalInclTax(),
                'quantity' => (int) $item->getQtyOrdered(),
                'requires_av' => match ($mode) {
                    1 => (int) ($product && $product->getAgeVerifiedVerifyProductYesNo() == 1),
                    2 => (int) ($product && array_intersect((array) $product->getCategoryIds(), $selectedCategories)),
                    default => 1,
                },
            ];
        }

        return [
            'user' => [[
                'email' => $order->getCustomerEmail(),
                'firstname' => $billingAddress->getFirstname(),
                'lastname' => $billingAddress->getLastname(),
                'dob' => (string) $order->getCustomerDob(),
                'order_id' => (string) $order->getId(),
                'order_ref' => $order->getIncrementId(),
                'site_transaction_id' => $order->getIncrementId(),
                'address' => [
                    'address1' => $billingAddress->getStreet()[0] ?? '',
                    'address2' => '',
                    'address3' => '',
                    'city' => $billingAddress->getCity(),
                    'postcode' => $billingAddress->getPostcode(),
                    'county' => $billingAddress->getRegion(),
                    'country' => $billingAddress->getCountryId(),
                ],
            ]],
            'cart' => [['products' => $products]],
        ];
    }

    /**
     * Push an order to AgeChecked Plus+. It ignores orders it already has, so a repeat send is harmless.
     */
    public function ageVerifyOrder(Order $order): bool
    {
        $payload = $this->getTransaction($order);
        if ($payload === null) {
            return false;
        }

        // Secure URL explicitly: cron has no request, so the default would be web/unsecure/base_url
        $baseUrl = (string) $order->getStore()->getBaseUrl(UrlInterface::URL_TYPE_LINK, true);
        $url = parse_url($baseUrl);
        if ($url === false || empty($url['scheme']) || empty($url['host'])
            || !in_array(strtolower($url['scheme']), ['http', 'https'], true)
        ) {
            $this->logger->warning(sprintf(
                'AgeChecked Plus+: invalid store base URL for order %s',
                $order->getIncrementId()
            ));
            return false;
        }

        $scheme = strtolower($url['scheme']);
        // Browsers send the host lowercased
        $host = strtolower($url['host']);
        $origin = $scheme . '://' . $host;
        if (isset($url['port']) && !(($scheme === 'http' && $url['port'] === 80)
            || ($scheme === 'https' && $url['port'] === 443))
        ) {
            $origin .= ':' . $url['port'];
        }

        try {
            $this->clientFactory->create(['config' => ['timeout' => 15]])->request(
                'POST',
                $this->unityConfig->getUnityOrderUrl(),
                [
                    'query' => ['domain' => $host],
                    'headers' => ['Origin' => $origin],
                    'json' => $payload,
                ]
            );
            return true;
        } catch (GuzzleException $e) {
            $this->logger->warning(sprintf(
                'AgeChecked Plus+: failed to send order %s: %s',
                $order->getIncrementId(),
                $e->getMessage()
            ));
            return false;
        }
    }
}