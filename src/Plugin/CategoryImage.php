<?php

namespace Zamoroka\ProductionImages\Plugin;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Category\FileInfo;
use Zamoroka\ProductionImages\Helper\Config;
use Zamoroka\ProductionImages\Model\ImageDownloader;

class CategoryImage
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
     * @param Category $subject
     * @param string $attributeCode
     *
     * @return string[]
     */
    public function beforeGetImageUrl(Category $subject, $attributeCode = 'image')
    {
        if (!$this->config->isEnabled()) {
            return [$attributeCode];
        }
        $image = $subject->getData($attributeCode);
        if (!$image || !is_string($image)) {
            return [$attributeCode];
        }
        $this->imageDownloader->downloadImage($this->getFilePath($image));

        return [$attributeCode];
    }

    /**
     * @param string $image
     *
     * @return string
     */
    private function getFilePath(string $image)
    {
        return FileInfo::ENTITY_MEDIA_PATH . DIRECTORY_SEPARATOR . $image;
    }
}
