<?php

namespace WeltPixel\DesignElements\Helper;

/**
 * @SuppressWarnings(PHPMD.TooManyFields)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
	/**
	 * @var array
	 */
	protected $_elementsOptions;

	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\App\Helper\Context $context
	 */
	public function __construct(
			\Magento\Framework\App\Helper\Context $context
	) {
		parent::__construct($context);

		$this->_elementsOptions = $this->scopeConfig->getValue('weltpixel_design_elements', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
	}

	/**
	 * @return string
	 */
	public function getBttOptionsJson()
	{
		$options = [];
		foreach ($this->getBttOptions() as $key => $value) {
			$value = (int) trim($value);
			$options[$key] = $value;
		}

		return json_encode($options);
	}

	/**
	 * @return array
	 */
	public function getBttOptions() {
		return array(
			'offset' => $this->_elementsOptions['general']['btt_offset'],
			'offsetOpacity' => $this->_elementsOptions['general']['btt_offset_opacity'],
			'scrollTopDuration' => $this->_elementsOptions['general']['btt_duration'],
		);
	}

    /**
     * @return bool
     */
	public function isResponsiveHelpersEnabled() {
        return $this->_elementsOptions['general']['responsive_helpers'];
    }

    /**
     * @return bool
     */
    public function getCollapsibleWidgetTopJump() {
        return $this->_elementsOptions['general']['collapsible_top_jump'];
    }

    /**
     * @return string
     */
    public function getBttProductPageLabel() {
        return trim($this->_elementsOptions['btt_product_page']['btt_text_label']);
    }

    /**
     * @return boolean
     */
    public function isBttProductPageImageEnabled() {
        return (boolean)$this->_elementsOptions['btt_product_page']['btt_product_img'];
    }

    /**
     * @return boolean
     */
    public function isBttProductPageReviewsEnabled() {
        return (boolean)$this->_elementsOptions['btt_product_page']['btt_reviews'];
    }

    /**
     * @return boolean
     */
    public function isBttProductPagePriceEnabled() {
        return (boolean)$this->_elementsOptions['btt_product_page']['btt_product_price'];
    }

    /**
     * @return string
     */
    public function getBttMobileProductPageDisplayOption() {
        return (string)$this->_elementsOptions['btt_product_page']['btt_product_mobile_option'];
    }

    /**
     * @return string
     */
    public function getBttProductPageBackgroundColor() {
        return (string)$this->_elementsOptions['btt_product_page']['btt_background_color'];
    }

    /**
     * @return string
     */
    public function getBttProductPageButtonBoxShadow() {
        return (string)$this->_elementsOptions['btt_product_page']['btt_box_shadow'];
    }

    /**
     * @return string
     */
    public function getBttProductPageButtonBorderWidth() {
        return (string)$this->_elementsOptions['btt_product_page']['btt_border_width'];
    }

    /**
     * @return string
     */
    public function getBttProductPageButtonBorderColor() {
        return (string)$this->_elementsOptions['btt_product_page']['btt_border_color'];
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageType
     * @return string
     */
    public function getAltImage($product, $imageType)
    {
        $existingMediaGalleryEntries = $product->getMediaGalleryEntries();
        foreach ($existingMediaGalleryEntries as $entry) {
            $imageTypes = $entry->getTypes();
            if (in_array($imageType, $imageTypes)) {
                return $entry->getLabel();
            }
        }

        return '';

    }
}
