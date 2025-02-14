# Additional Merchant ID module for Magento

Allow merchants to use additional merchant ID for some products


## Installation

Install module by composer

```bash
  composer require tabby/m2-sub --no-update
  composer update
```

Check module status

```bash
  php bin/magento module:status Tabby_Sub
```

Enable module

```bash
  php bin/magento module:enable Tabby_Sub
```

Upgrade magento database

```bash
  php bin/magento setup:upgrade
```

Generate DI configuration

```bash
  php bin/magento setup:di:compile
```

Deploy static content

```bash
  php bin/magento setup:static-content:deploy
```
