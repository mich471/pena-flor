<?php
/**
 * @category    WeltPixel
 * @package     WeltPixel_RecentlyViewedBar
 * @copyright   Copyright (c) 2018 Weltpixel
 * @author      Nagy Attila @ Weltpixel TEAM
 */

namespace WeltPixel\RecentlyViewedBar\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\State;
use Magento\Cms\Model\BlockFactory;

/**
 * Class InstallData
 * @package WeltPixel\RecentlyViewedBar\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var State
     */
    protected $appState;
    /**
     * @var BlockFactory
     */
    private $blockFactory;


    /**
     * InstallData constructor.
     * @param StoreManagerInterface $storeManager
     * @param State $appState
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        State $appState,
        BlockFactory $blockFactory
    )
    {
        $this->storeManager = $storeManager;
        $this->appState = $appState;
        $this->blockFactory = $blockFactory;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    )
    {
        try {
            if(!$this->appState->isAreaCodeEmulated()) {
                $this->appState->setAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND);
            }
        } catch (\Exception $ex) {
        }

        $content = <<<EOT
<div class="arv-cms-img"><img class="arv-desktop-img" src="{{view url='WeltPixel_RecentlyViewedBar/images/desktop_sample.png'}}"> <img class="arv-mobile-img" src="{{view url='WeltPixel_RecentlyViewedBar/images/mobile_sample.png'}}"></div>
EOT;

        $cmsBlockData = [
            'title' => 'Recently Viewed Bar - Sample Block',
            'identifier' => 'rvb_sample_block',
            'content' => $content,
            'is_active' => 1,
            'stores' => [0],
            'sort_order' => 0
        ];

        try {
            $this->blockFactory->create()->setData($cmsBlockData)->save();
        } catch (\Exception $ex) {
        }

    }
}
