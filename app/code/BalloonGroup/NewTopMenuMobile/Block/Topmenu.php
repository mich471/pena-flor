<?php
	namespace BalloonGroup\NewTopMenuMobile\Block;
	 
	class Topmenu extends \Magento\Framework\View\Element\Template
	{
		public function getMediaUrl()
		{
			return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
		}   
	}