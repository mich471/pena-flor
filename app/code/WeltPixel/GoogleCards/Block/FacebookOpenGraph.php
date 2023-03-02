<?php

namespace WeltPixel\GoogleCards\Block;

use Magento\Review\Model\ResourceModel\Review\CollectionFactory;
use WeltPixel\GoogleCards\Model\Config\FileUploader\FileProcessor as ImageFileProcessor;

class FacebookOpenGraph extends GoogleCards
{
    /**
     * @var ImageFileProcessor
     */
    protected $imageFileProcessor;

    /**
     * GoogleCards constructor.
     * @param ImageFileProcessor $imageFileProcessor
     * @param \Magento\Catalog\Block\Product\Context $productContext
     * @param \WeltPixel\GoogleCards\Helper\Data $helper
     * @param \Magento\Review\Model\Review\SummaryFactory $reviewSummaryFactory
     * @param CollectionFactory $_reviewsFactory
     * @param \Magento\Theme\Block\Html\Header\Logo $logo
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        ImageFileProcessor $imageFileProcessor,
        \Magento\Catalog\Block\Product\Context $productContext,
        \WeltPixel\GoogleCards\Helper\Data $helper,
        \Magento\Review\Model\Review\SummaryFactory $reviewSummaryFactory,
        \Magento\Review\Model\ResourceModel\Review\CollectionFactory $_reviewsFactory,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->imageFileProcessor = $imageFileProcessor;
        parent::__construct($productContext, $helper, $reviewSummaryFactory, $_reviewsFactory, $logo, $context, $data);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getDescription($product)
    {
        if ($this->_helper->getFacebookDescriptionType() == 1) {
            return nl2br($product->getData('description'));
        } elseif ($this->_helper->getFacebookDescriptionType() == 2) {
            return nl2br($product->getData('meta_description'));
        } else {
            return nl2br($product->getData('short_description'));
        }
    }

    /**
     * @return string
     */
    public function getSiteName()
    {
        return $this->_helper->getFacebookSiteName();
    }

    /**
     * @return string
     */
    public function getAppId()
    {
        return $this->_helper->getFacebookAppId();
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        $priceOption = $this->_helper->getFacebookOpenGraphPrice();
        return $this->_calculatePrice($priceOption);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getRetailerId($product)
    {
        $idOption = $this->_helper->getFacebookRetailerId();
        $retailerItemId = '';

        switch ($idOption) {
            case 'sku':
                $retailerItemId = $product->getData('sku');
                break;
            case 'id':
            default:
            $retailerItemId = $product->getId();
                break;
        }

        return $retailerItemId;
    }

    /**
     * @param \Magento\Cms\Model\Page $page
     * @return string
     */
    public function getCmsPageImage($page)
    {
        $ogImageSrc = '';
        $ogImage = $page->getData('og_meta_image');
        if (strlen($ogImage)) {
            $ogImageSrc = $this->imageFileProcessor->getFinalMediaUrl($ogImage);
        }

        return $ogImageSrc;
    }
}
