<?php

namespace Zamoroka\ProductionImages\Plugin;

use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Magento\Catalog\Model\View\Asset\Placeholder;
use Zamoroka\ProductionImages\Helper\Config;
use Zamoroka\ProductionImages\Model\ImageDownloader;

class CatalogAssetPlaceholderImage
{
    /** @var Config */
    private $config;
    /** @var ImageDownloader */
    private $imageDownloader;
    /** @var MediaConfig */
    private $mediaConfig;

    /**
     * CategoryImage constructor.
     *
     * @param Config $config
     * @param ImageDownloader $imageDownloader
     * @param MediaConfig $mediaConfig
     */
    public function __construct(
        Config $config,
        ImageDownloader $imageDownloader,
        MediaConfig $mediaConfig
    ) {
        $this->config = $config;
        $this->imageDownloader = $imageDownloader;
        $this->mediaConfig = $mediaConfig;
    }

    /**
     * @param Placeholder $subject
     *
     * @return array
     */
    public function beforeGetUrl(Placeholder $subject)
    {
        if (!$this->config->isEnabled()) {
            return [];
        }
        $this->imageDownloader->downloadImage($this->getFilePath($subject));

        return [];
    }

    /**
     * @param Placeholder $placeholder
     *
     * @return string
     */
    private function getFilePath(Placeholder $placeholder): string
    {
        return \implode(DIRECTORY_SEPARATOR, [
            $this->mediaConfig->getBaseMediaPath(),
            $placeholder->getModule(),
            $placeholder->getFilePath()
        ]);
    }
}
