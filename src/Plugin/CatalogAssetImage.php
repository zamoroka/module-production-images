<?php

namespace Zamoroka\ProductionImages\Plugin;

use Magento\Catalog\Model\View\Asset\Image;
use Zamoroka\ProductionImages\Helper\Config;
use Zamoroka\ProductionImages\Model\ImageDownloader;

class CatalogAssetImage
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
     * @param Image $subject
     *
     * @return array
     */
    public function beforeGetUrl(Image $subject)
    {
        if (!$this->config->isEnabled()) {
            return [];
        }
        $this->imageDownloader->downloadImage($this->getFilePath($subject));

        return [];
    }

    /**
     * @param Image $image
     *
     * @return string
     */
    private function getFilePath(Image $image): string
    {
        return $image->getSourceFile();
    }
}
