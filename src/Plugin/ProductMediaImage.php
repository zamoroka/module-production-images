<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
declare(strict_types=1);

namespace Zamoroka\ProductionImages\Plugin;

use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Zamoroka\ProductionImages\Helper\Config;
use Zamoroka\ProductionImages\Model\ImageDownloader;

class ProductMediaImage
{
    /** @var Config */
    private $config;
    /** @var ImageDownloader */
    private $imageDownloader;

    /**
     * CategoryImage constructor.
     *
     * @param Config $config
     * @param ImageDownloader $imageDownloader
     */
    public function __construct(
        Config $config,
        ImageDownloader $imageDownloader
    ) {
        $this->config = $config;
        $this->imageDownloader = $imageDownloader;
    }

    /**
     * @param MediaConfig $subject
     * @param string $file
     *
     * @return array
     */
    public function beforeGetMediaUrl(MediaConfig $subject, $file)
    {
        if (!$this->config->isEnabled()) {
            return [$file];
        }
        $this->imageDownloader->downloadImage($this->getFilePath($subject, $file));

        return [$file];
    }

    /**
     * @param MediaConfig $config
     * @param $file
     *
     * @return string
     */
    private function getFilePath(MediaConfig $config, string $file): string
    {
        return $config->getBaseMediaPath() . DIRECTORY_SEPARATOR . $file;
    }
}
