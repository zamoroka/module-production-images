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
    public function afterGetSourceFile(Image $subject, $file)
    {
        if (!$this->config->isEnabled()) {
            return $file;
        }
        $this->imageDownloader->downloadImage($file);

        return $file;
    }
}
