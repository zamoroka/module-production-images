<?php

namespace Zamoroka\ProductionImages\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    public const XML_PATH_PRODUCTION_IMAGES = 'production_images/';

    /**
     * @param $field
     * @param null $storeId
     *
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeId);
    }

    /**
     * @param $code
     * @param null $storeId
     *
     * @return mixed
     */
    public function getGeneralConfig($code, $storeId = null)
    {

        return $this->getConfigValue(self::XML_PATH_PRODUCTION_IMAGES . 'general/' . $code, $storeId);
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->getGeneralConfig('enabled');
    }

    /**
     * @return string
     */
    public function getProductionMediaUrl(): string
    {
        return (string)$this->getGeneralConfig('production_media_url');
    }
}
