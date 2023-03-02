<?php

namespace WeltPixel\CmsBlockScheduler\Plugin;

class BeforeSave
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;
    protected $_date;

    /**
     * BeforeSave constructor.
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Stdlib\DateTime\DateTime $date
    )
    {
        $this->_request = $request;
        $this->_date = $date;
    }

    /**
     * @param \Magento\Cms\Model\BlockRepository $subject
     * @param $block
     */
    public function beforeSave(\Magento\Cms\Model\BlockRepository $subject, $block) {
        $postData = $this->_request->getPost();
        if(empty($postData['valid_from']) || $postData['valid_from'] == 'Invalid date') {
            $block->setData('valid_from', $this->_date->gmtDate());
        }
        if(empty($postData['valid_to']) || $postData['valid_to'] == 'Invalid date') {
            $endDate = $this->_date->gmtDate('Y-m-d H:i:s', '2099-12-31 23:00:00');
            $block->setData('valid_to', $endDate);
        }


    }
}