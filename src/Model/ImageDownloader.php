<?php

namespace Zamoroka\ProductionImages\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem;
use Psr\Log\LoggerInterface;
use Zamoroka\ProductionImages\Helper\Config;

class ImageDownloader
{
    /** @var Config */
    private $config;
    /** @var LoggerInterface */
    private $logger;
    /** @var Filesystem\Directory\WriteInterface */
    private $directory;
    /** @var Filesystem */
    private $filesystem;

    /**
     * @param Filesystem $filesystem
     * @param LoggerInterface $logger
     *
     * @param Config $config
     *
     * @throws FileSystemException
     */
    public function __construct(
        Filesystem $filesystem,
        LoggerInterface $logger,
        Config $config
    ) {
        $this->filesystem = $filesystem;
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $this->logger = $logger;
        $this->config = $config;
    }

    /**
     * @param string $filePath
     */
    public function downloadImage(string $filePath)
    {
        if (!$this->directory->isExist($filePath)) {
            try {
                $url = $this->config->getProductionMediaUrl() . $filePath;
                $this->directory->create(\dirname($filePath));
                $this->directory->writeFile($filePath, \file_get_contents($url));
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage());
            }
        }
    }
}
