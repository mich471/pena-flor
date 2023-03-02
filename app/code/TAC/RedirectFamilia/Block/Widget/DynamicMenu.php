<?php
namespace TAC\RedirectFamilia\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Customer\Model\SessionFactory;

class DynamicMenu extends Template implements BlockInterface {

    /** DEFAULT MENU */
    CONST DEFAULT_MENU = 'tac-menu';

    /** @var SessionFactory $_customerSessionFactory */
    protected $_customerSessionFactory;

    /** @var array $_userGroupMenu */
    protected $_userGroupMenuIdentifier = [];

    protected $_isScopePrivate = true;

    /**
     * DynamicMenu constructor.
     * @param Template\Context $context
     * @param array $data
     * @param SessionFactory $customerSessionFactory
     */
    public function __construct
    (
        Template\Context $context,
        array $data = [],
        SessionFactory $customerSessionFactory
    )
    {
        $this->_customerSessionFactory = $customerSessionFactory;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Parse identifiers
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->_parseIdentifiers($this->getData('identifiers'));
        return $this;
    }

    /**
     * Set Menu HTML
     */
    protected function _toHtml()
    {
        $identifier = $this->_getGroupMenuIdentifier();
        return $this->_getMegaMenuHtml($identifier);
    }

    /**
     * Fill $this->_userGroupMenuIdentifier
     * @usage 6|bemberg-familia||7|bemberg-familia
     * @param $string
     */
    protected function _parseIdentifiers($string)
    {
        $identifiers = explode('||', $string);
        if (is_array($identifiers)) {
            foreach ($identifiers as $identifier) {
                $identifier = explode('|', $identifier);
                if (!isset($identifier[0]) || !isset($identifier[1])) {
                    continue;
                }
                $userGroup = (int)$identifier[0];
                $menuIdentifier = $identifier[1];
                $this->_userGroupMenuIdentifier[(int)$userGroup] = $menuIdentifier;
            }
        }
    }

    /**
     * Get menu group based identifier
     */
    protected function _getGroupMenuIdentifier()
    {
        $customerSession = $this->_customerSessionFactory->create();
        if (!$customerSession->isLoggedIn()) {
            return '';
        }
        $user = $customerSession->getCustomer();
        $identifier = self::DEFAULT_MENU;
        if (isset($this->_userGroupMenuIdentifier[$user->getGroupId()])) {
            $identifier = $this->_userGroupMenuIdentifier[$user->getGroupId()];
        }
        return $identifier;
    }

    /**
     * Get Megamenu Html
     * @param $identifier
     * @return String
     */
    protected function _getMegaMenuHtml($identifier)
    {
        $htmlMenu = $this->getLayout()
            ->createBlock('Codazon\MegaMenu\Block\Widget\Megamenu')
            ->setMenu($identifier)
            ->toHtml();
        if ($identifier == self::DEFAULT_MENU) {
            return '<div class="cdz-main-menu">' . $htmlMenu . '</div>';
        }
        return $htmlMenu;
    }

}