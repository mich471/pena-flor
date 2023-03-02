<?php

namespace WeltPixel\Quickview\Plugin;

class RemoveCartMessages
{
    /**
     * @var \WeltPixel\Quickview\Helper\Data $helper
     */
    protected $helper;

    /**
     * @param \WeltPixel\Quickview\Helper\Data $helper
     */
    public function __construct(
        \WeltPixel\Quickview\Helper\Data $helper
        ) {
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Theme\CustomerData\Messages $subject
     * @param $result
     * @return mixed
     */
    public function afterGetSectionData(
        \Magento\Theme\CustomerData\Messages $subject,
        $result
        )
    {
        if (!$this->helper->isAjaxCartEnabled()) {
            return $result;
        }

        if (isset($result['messages'])) {
            foreach ($result['messages'] as $id => $messageDetails) {
                if (($messageDetails['type'] == 'success') && (!strlen($messageDetails['text']))) {
                    unset($result['messages'][$id]);
                    $result['wp_messages'] = true;
                }
            }
        }

        return $result;

    }
}
