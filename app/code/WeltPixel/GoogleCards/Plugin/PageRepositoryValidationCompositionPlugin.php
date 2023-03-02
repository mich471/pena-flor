<?php

namespace WeltPixel\GoogleCards\Plugin;

use Magento\Cms\Api\Data\PageInterface;
use Magento\Cms\Model\PageRepository\ValidationComposite;

/**
 * Class PageRepositoryValidationCompositionPlugin
 * @package WeltPixel\GoogleCards\Plugin
 */
class PageRepositoryValidationCompositionPlugin
{
    /**
     * @param ValidationComposite $subject
     * @param PageInterface $page
     * @return PageInterface
     */
    public function beforeSave(
        ValidationComposite $subject,
        PageInterface $page
    ) {
        if (!$page->getData('og_meta_image')) {
            $page->setData('og_meta_image', '');
        }
        return [$page];
    }
}
