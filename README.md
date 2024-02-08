# AgeChecked Plus+ (Magento 2.4+ Opensource)
## Description
This module provides age verification functionality for Magento Opensource stores.

## Installation
### Composer Installation (recommended)
1. Use Composer to install the module:
```
composer require Verifico/Ageverify
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
1. Download the module from GitHub or any other source.

2. Extract the downloaded archive and upload the contents to your Magento 2 root directory.

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

## Support
For support and inquiries, please contact clientservice@agechecked.com.

## License
This project is licensed under the GNU General Public License v3.0 (GPL-3.0). See the LICENSE.md file for details.
