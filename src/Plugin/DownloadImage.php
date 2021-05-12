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
    /** @var Config */
    private $config;
    /** @var ImageDownloader */
    private $imageDownloader;

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

    private function cleanFilePath(string $filePath): string
    {
        // remove `media/` and `cache/.*/` from $filePath
        return \preg_replace('/media\/|cache\/.*?\//i', '', $filePath);
    }
}
