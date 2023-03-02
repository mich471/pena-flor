<?php
namespace WeltPixel\SpeedOptimization\Model;

use Magento\Framework\View\Asset\File\FallbackContext as FileFallbackContext;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class FileManager
{
    /**
     * @var AssetRepository
     */
    private $assetRepo;

    /**
     * @var FileFallbackContext
     */
    private $staticContext;

    /**
     * @param AssetRepository $assetRepo
     */
    public function __construct(AssetRepository $assetRepo)
    {
        $this->assetRepo = $assetRepo;
        $this->staticContext = $assetRepo->getStaticViewFileContext();
    }

    /**
     * @param string $fullActionName
     * @return \Magento\Framework\View\Asset\File
     */
    public function createJsBundleAsset($fullActionName)
    {
        $formattedActionName = str_replace("_", "-", $fullActionName);
        $relPath = $this->staticContext->getConfigPath() . '/bundles/' . $formattedActionName . '.js';
        return $this->assetRepo->createArbitrary($relPath, '');
    }

    /**
     * @return \Magento\Framework\View\Asset\File
     */
    public function createSharedJsBundleAsset()
    {
        $relPath = $this->staticContext->getConfigPath() . '/bundles/shared.js';
        return $this->assetRepo->createArbitrary($relPath, '');
    }
}
