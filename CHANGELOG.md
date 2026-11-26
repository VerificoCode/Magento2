# Changelog

## 1.2.0 - 2026-11-26

### Added
- Background send: every 5 minutes, orders still in `Pending` age verification are sent to AgeChecked Plus+ once, in case the checkout success page never loaded. On by default, limited to paid orders placed within "Only Send Orders Placed Within (hours)" (48 by default).
- "Hold Orders Awaiting Age Verification" setting (off by default). When on, orders that need age verification are put on hold as they are placed. Orders still waiting for payment are not held.
- Age Verification Status row on the admin order view page.
- README: Age Verification Status values, the Magento API calls AgeChecked Plus+ can make and the permission each needs, and how to restrict API access to AgeChecked Plus+ IP addresses.

### Changed
- Requires PHP 8.1 to 8.5 and Magento 2.4.
- The custom order statuses (`av_pending`, `av_low_risk`, `av_high_risk` and the others) are removed. The Age Verification Status field replaces them, and orders on those statuses move to their state's default status.
- The success page data and tracking script are output CSP-safe and escaped.
- The Age Verification Status in the order grid and order view renders simple HTML (`br`, `small`, `strong`, `b`, `i`, `em`, `span`, `style`); other tags and attributes are removed.
- The status column on the order grid now updates for storefront, admin and API saves.
- The integration now requests `Magento_Sales::api_actions`, which Magento 2.4.8 and later need to update the status, and `Magento_Sales::sales_invoice`, for finding and refunding invoices.
- Legacy setup scripts replaced with data patches.

### Fixed
- The "Allowed Countries" setting was ignored, so orders from every country were verified.
- An order containing a deleted product could fail at checkout.
- The success page block no longer replaces Magento's own checkout success block.
- The frontend layout no longer forces a one-column page layout.

### Removed
- The unused registration page and "Advanced" configuration block.
- The `frame-ancestors` CSP entry.

### Upgrade notes
- Run `bin/magento setup:upgrade` and `bin/magento setup:di:compile`.
- Reauthorise the "AgeCheckedPlus+Verifico" integration (System > Extensions > Integrations > Reauthorize) to grant the new permissions.
- Magento cron must be running for the background send.
