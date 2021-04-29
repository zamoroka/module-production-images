<?php
/**
 * Copyright © Vaimo Group. All rights reserved.
 * See LICENSE_VAIMO.txt for license details.
 */
declare(strict_types=1);

namespace Zamoroka\ProductionImages\Plugin;

use Magento\MediaStorage\Model\File\Storage\Synchronization;
use Zamoroka\ProductionImages\Helper\Config;
use Zamoroka\ProductionImages\Model\ImageDownloader;

class DownloadImage
{
    public function __construct(
        Config $config,
        ImageDownloader $imageDownloader
    ) {

        $this->config = $config;
        $this->imageDownloader = $imageDownloader;
    }

    /**
     * @param Synchronization $subject
     * @param string $relativeFileName
     *
     * @return array
     */
    public function beforeSynchronize(
        Synchronization $subject,
        $relativeFileName
    ) {
        if (!$this->config->isEnabled()) {
            return [$relativeFileName];
        }
        if (!$relativeFileName || !is_string($relativeFileName)) {
            return [$relativeFileName];
        }
        $this->imageDownloader->downloadImage($this->cleanFilePath($relativeFileName));

        return [$relativeFileName];
    }

    private function cleanFilePath($filePath)
    {
        $prefix = 'media/';
        if (substr($filePath, 0, strlen($prefix)) == $prefix) {
            $filePath = substr($filePath, strlen($prefix));
        }

        return $filePath;
    }
}
