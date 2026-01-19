## Get images from production server
 
##### Installation

```
composer require zamoroka/module-production-images --dev
```

##### Configuration

1. Enable module
```shell
bin/magento module:enable Zamoroka_ProductionImages
bin/magento config:set production_images/general/enabled 1
```
2. Provide production media URL under `Store -> Configuration -> Zamoroka -> Production images -> Production media URL`. e.g https://example.com/media/
or using command
```shell
bin/magento config:set production_images/general/production_media_url "https://example.com/media/"
```
3. Clear config and image cache
```
bin/magento cache:flush
```
