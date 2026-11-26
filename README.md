# AgeChecked Plus+ (Magento 2.4+ Opensource)

## Description

This module provides age verification functionality for Magento Opensource stores.

## Installation

### Composer Installation (recommended)

1. Use Composer to install the module:

```
composer require verifico/ageverify
```

2. Enable the module:

```
php bin/magento module:enable Verifico_Ageverify
php bin/magento setup:upgrade
```

3. Flush the cache:

```
php bin/magento cache:flush
```

### Manual Installation

1. Download the module from the [Releases](https://github.com/VerificoCode/Magento2/releases) page.

2. Extract the downloaded archive and upload the contents to `app/code/Verifico/Ageverify` in your Magento 2 root directory.

3. Enable the module:

```
php bin/magento module:enable Verifico_Ageverify
php bin/magento setup:upgrade
```

4. Flush the cache:

```
php bin/magento cache:flush
```

## Configuration

You can access the module settings by navigating to "Stores" > "Configuration" > "AgeChecked Plus+".

Requires Magento 2.4 (magento/framework 103) on PHP 8.1 to 8.5, with Magento cron running.

"Hold Orders Awaiting Age Verification" (off by default) puts orders that need age verification on hold when they are placed. Orders still waiting for payment (for example, at an external payment page) are not held, because Magento cannot invoice a held order. Held orders are released by the merchant, or by AgeChecked Plus+ where the merchant's package includes it.

"Send Pending Orders in Background" (on by default) sends orders still awaiting age verification to AgeChecked Plus+ every 5 minutes, in case the checkout success page never loaded (for example, the customer closed the browser after paying). Each order is sent once, after 15 minutes, and only if it is paid and was placed within "Only Send Orders Placed Within (hours)".

## Age Verification Status

Every order gets an Age Verification Status, shown in its own column on the order grid and on the order view page. Stores can filter the grid on this field to keep unverified orders from fulfilment, turn on "Hold Orders Awaiting Age Verification", or have AgeChecked Plus+ hold and release orders through the API.

| Status | Set by | Meaning |
| --- | --- | --- |
| `Pending` | Module, when the order is placed | The order needs age verification. The success page (or the background send) passes it to AgeChecked Plus+. |
| `Pending Age Verification` | AgeChecked Plus+ | AgeChecked Plus+ has received the order and is waiting for the customer to be verified. |
| `Age Verified` | AgeChecked Plus+ | The customer has been age verified. |
| `Age Verified*` | AgeChecked Plus+ | The customer has been age verified, but with details different from those on the order. The order comments show the differences. |
| `Age Verification Not Required` | Module, when the order is placed | Nothing in the order matches the "Verify Mode" rules, or the billing country is not in the selected countries. |
| `Age Verification Disabled` | Module, when the order is placed | The module was turned off for the store when the order was placed. |
| (empty, shown as ⓘ) | — | The order was placed before the module was installed. |

The status may contain simple HTML (`br`, `small`, `strong`, `b`, `i`, `em`, `span`, `style`). Any other tag (its text is kept) and any attribute other than `id`, `class`, `href`, `title` and `style` is removed before it is shown.

## Magento API calls

AgeChecked Plus+ calls the store's Magento REST API with the access token of the "AgeCheckedPlus+Verifico" integration (System > Extensions > Integrations), sent as `Authorization: Bearer <token>` with `Content-Type: application/json`.

These are all the calls AgeChecked Plus+ can make. Which ones it actually makes depends on the merchant's AgeChecked Plus+ package and configuration: for example, a store may not use holds or automatic refunds at all.

`{id}` is the order's `entity_id`, not its increment ID. The module sends it as `order_id`, which AgeChecked Plus+ stores as the transaction's `site_transaction_id`. The Age Verification Status is read and written as the order extension attribute `unity_age_verification_status`.

| Purpose | Method | Path | Magento permission |
| --- | --- | --- | --- |
| Get order data (including its current `status`) | GET | `/rest/V1/orders/{id}` | `Magento_Sales::actions_view` |
| Hold the order | POST | `/rest/V1/orders/{id}/hold` | `Magento_Sales::hold` |
| Release the order | POST | `/rest/V1/orders/{id}/unhold` | `Magento_Sales::unhold` |
| Set the Age Verification Status | POST | `/rest/V1/orders/` | `Magento_Sales::create` up to Magento 2.4.7, `Magento_Sales::api_actions` from 2.4.8 |
| Add an order comment | POST | `/rest/V1/orders/{id}/comments` | `Magento_Sales::comment` |
| Find the order's invoice | GET | `/rest/V1/invoices?searchCriteria[...]` | `Magento_Sales::sales_invoice` |
| Refund the invoice online | POST | `/rest/V1/invoice/{invoiceId}/refund` | `Magento_Sales::sales_invoice` |

New installs grant these permissions automatically. Stores upgrading from an earlier version must reauthorise the integration (System > Extensions > Integrations > Reauthorize) so it has exactly the permissions listed above.

To limit who can call the API with the integration's token, ask clientservice@agechecked.com for the IP addresses AgeChecked Plus+ sends requests from, and allow only those at your firewall, web server or CDN.

## Support

For support and inquiries, please contact clientservice@agechecked.com.

## License

This project is licensed under the GNU General Public License v3.0 (GPL-3.0). See the LICENSE.md file for details.
