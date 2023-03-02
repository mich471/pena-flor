<?php

	namespace BalloonGroup\NewTopMenuMobile\Plugin;

	use Magento\Cms\Model\BlockRepository;
	use Magento\Cms\Model\Template\FilterProvider;
	use Magento\Framework\Data\Tree\NodeFactory;
	use Magento\Framework\Data\TreeFactory;
	use Magento\Framework\DataObject;
	use Magento\Framework\View\Element\Template;
	use WeltPixel\NavigationLinks\Helper\Data as WpHelper;

	class TopMenuPlugin {
		protected $layoutFactory;

		public function __construct(\Magento\Framework\View\LayoutFactory $layoutFactory)
		{
		    $this->layoutFactory = $layoutFactory;
		}

		public function afterGetHtml($subject, $result) {
		    $html = $result;
		    $layout = $this->layoutFactory->create();
		    $html .= $layout->createBlock("BalloonGroup\NewTopMenuMobile\Block\Topmenu")->setTemplate("BalloonGroup_NewTopMenuMobile::leanblock.phtml")->toHtml();
		    return $html;
		}

	}