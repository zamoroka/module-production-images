<?php

namespace Zamoroka\ProductionImages\Plugin;

use Magento\Catalog\Block\Product\ImageFactory;
use Magento\Catalog\Helper\Image as ImageHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Image\ParamsBuilder;
use Magento\Catalog\Model\Product\Media\Config as MediaConfig;
use Magento\Catalog\Model\View\Asset\PlaceholderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\File\WriteInterface as FileWriteInterface;
use Magento\Framework\View\ConfigInterface;
use Psr\Log\LoggerInterface;

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
    /** @var LoggerInterface */
    private $logger;
    /** @var Filesystem\Directory\WriteInterface */
    private $directory;
    /** @var PlaceholderFactory */
    private $viewAssetPlaceholderFactory;

    /**
     * @param ConfigInterface $presentationConfig
     * @param PlaceholderFactory $viewAssetPlaceholderFactory
     * @param ParamsBuilder $imageParamsBuilder
     * @param MediaConfig $mediaConfig
     * @param Filesystem $filesystem
     * @param LoggerInterface $logger
     *
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        ConfigInterface $presentationConfig,
        PlaceholderFactory $viewAssetPlaceholderFactory,
        ParamsBuilder $imageParamsBuilder,
        MediaConfig $mediaConfig,
        Filesystem $filesystem,
        LoggerInterface $logger
    ) {
        $this->presentationConfig = $presentationConfig;
        $this->viewAssetPlaceholderFactory = $viewAssetPlaceholderFactory;
        $this->imageParamsBuilder = $imageParamsBuilder;
        $this->filesystem = $filesystem;
        $this->mediaConfig = $mediaConfig;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->logger = $logger;
    }

    /**
     * @param Product $product
     * @param string $imageId
     * @param array|null $attributes
     *
     * @return array
     */
    public function beforeCreate(ImageFactory $imgFactory, Product $product, string $imageId, array $attributes = null)
    {
        $viewImageConfig = $this->presentationConfig->getViewConfig()->getMediaAttributes(
            'Magento_Catalog',
            ImageHelper::MEDIA_TYPE_CONFIG_NODE,
            $imageId
        );
        $imageMiscParams = $this->imageParamsBuilder->build($viewImageConfig);
        $originalFilePath = $product->getData($imageMiscParams['image_type']);
        if ($originalFilePath === null || $originalFilePath === 'no_selection') {
            $imageAsset = $this->viewAssetPlaceholderFactory->create(['type' => $imageMiscParams['image_type']]);
            $filePath = $this->mediaConfig->getMediaPath(
                $imageAsset->getModule() . DIRECTORY_SEPARATOR . $imageAsset->getFilePath()
            );
        } else {
            $filePath = $this->mediaConfig->getMediaPath($originalFilePath);
        }
        if (!$this->directory->isExist($filePath)) {
            try {
                $url = 'https://example.com/media/' . $filePath;
                $this->directory->create(\dirname($filePath));
                $this->directory->writeFile($filePath, \file_get_contents($url));
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        }

        return [$product, $imageId, $attributes];
    }
}
