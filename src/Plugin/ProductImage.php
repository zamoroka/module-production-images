<?php

namespace Zamoroka\ProductionImages\Plugin;

use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Image\ParamsBuilder;
use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Magento\Catalog\Model\View\Asset\PlaceholderFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\WriteInterface as FileWriteInterface;
use Magento\Framework\View\ConfigInterface;
use Zamoroka\ProductionImages\Helper\Config;
use Zamoroka\ProductionImages\Model\ImageDownloader;

class ProductImage
{
    /** @var FileWriteInterface */
    protected $stream;
    /** @var ConfigInterface */
    private $presentationConfig;
    /** @var ParamsBuilder */
    private $imageParamsBuilder;
    /** @var Filesystem */
    private $filesystem;
    /** @var MediaConfig */
    private $mediaConfig;
    /** @var PlaceholderFactory */
    private $placeholderFactory;
    /** @var Config */
    private $config;
    /** @var ImageDownloader */
    private $imageDownloader;

    /**
     * @param ConfigInterface $presentationConfig
     * @param PlaceholderFactory $placeholderFactory
     * @param ParamsBuilder $imageParamsBuilder
     * @param MediaConfig $mediaConfig
     * @param Filesystem $filesystem
     * @param Config $config
     * @param ImageDownloader $imageDownloader
     */
    public function __construct(
        ConfigInterface $presentationConfig,
        PlaceholderFactory $placeholderFactory,
        ParamsBuilder $imageParamsBuilder,
        MediaConfig $mediaConfig,
        Filesystem $filesystem,
        Config $config,
        ImageDownloader $imageDownloader
    ) {
        $this->presentationConfig = $presentationConfig;
        $this->placeholderFactory = $placeholderFactory;
        $this->imageParamsBuilder = $imageParamsBuilder;
        $this->filesystem = $filesystem;
        $this->mediaConfig = $mediaConfig;
        $this->config = $config;
        $this->imageDownloader = $imageDownloader;
    }

    /**
     * @param ImageFactory $imgFactory
     * @param Product $product
     * @param string $imageId
     * @param array|null $attributes
     *
     * @return array
     */
    public function beforeCreate(ImageFactory $imgFactory, Product $product, string $imageId, array $attributes = null)
    {
        if (!$this->config->isEnabled()) {
            return [$product, $imageId, $attributes];
        }
        $filePath = $this->getFilePath($product, $imageId);
        $this->imageDownloader->downloadImage($filePath);

        return [$product, $imageId, $attributes];
    }

    /**
     * @param Product $product
     * @param string $imageId
     *
     * @return string
     */
    private function getFilePath(Product $product, string $imageId)
    {
        $viewImageConfig = $this->presentationConfig->getViewConfig()->getMediaAttributes(
            'Magento_Catalog',
            ImageHelper::MEDIA_TYPE_CONFIG_NODE,
            $imageId
        );
        $imageMiscParams = $this->imageParamsBuilder->build($viewImageConfig);
        $originalFilePath = $product->getData($imageMiscParams['image_type']);
        if ($originalFilePath === null || $originalFilePath === 'no_selection') {
            $imageAsset = $this->placeholderFactory->create(['type' => $imageMiscParams['image_type']]);
            $filePath = $this->mediaConfig->getMediaPath(
                $imageAsset->getModule() . DIRECTORY_SEPARATOR . $imageAsset->getFilePath()
            );
        } else {
            $filePath = $this->mediaConfig->getMediaPath($originalFilePath);
        }

        return $filePath;
    }
}
