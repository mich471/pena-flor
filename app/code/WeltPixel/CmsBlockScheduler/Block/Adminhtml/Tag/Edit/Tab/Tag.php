<?php

namespace WeltPixel\CmsBlockScheduler\Block\Adminhtml\Tag\Edit\Tab;

/**
 * Tag Edit tab.
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */
class Tag extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $_objectFactory;

    /**
     * @var \WeltPixel\CmsBlockScheduler\Model\Tag
     */
    protected $_tag;

    /**
     * constructor.
     *
     * @param \Magento\Backend\Block\Template\Context        $context
     * @param \Magento\Framework\Registry                    $registry
     * @param \Magento\Framework\Data\FormFactory            $formFactory
     * @param \Magento\Framework\DataObjectFactory           $objectFactory
     * @param \WeltPixel\CmsBlockScheduler\Model\Tag      $tag
     * @param array                                          $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \WeltPixel\CmsBlockScheduler\Model\Tag $tag,
        array $data = []
    ) {
        $this->_objectFactory = $objectFactory;
        $this->_tag = $tag;

        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * prepare layout.
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('page.title')->setPageTitle($this->getPageTitle());

        \Magento\Framework\Data\Form::setFieldsetElementRenderer(
            $this->getLayout()->createBlock(
                'WeltPixel\CmsBlockScheduler\Block\Adminhtml\Form\Renderer\Fieldset\Element',
                $this->getNameInLayout().'_fieldset_element'
            )
        );

        return $this;
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $dataObj = $this->_objectFactory->create();

        $model = $this->_coreRegistry->registry('tag');

        $dataObj->addData($model->getData());

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix($this->_tag->getFormFieldHtmlIdPrefix());

        $htmlIdPrefix = $form->getHtmlIdPrefix();

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Tag Details')]);
        
        if ($model->getId()) {
            $fieldset->addField('id', 'hidden', ['name' => 'id']);
        }

        $elements = [];

        $elements['title'] = $fieldset->addField(
            'title',
            'text',
            [
                'name'     => 'title',
                'label'    => __('Title'),
                'title'    => __('Title'),
                'required' => true,
            ]
        );

        $form->addValues($dataObj->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * @return mixed
     */
    public function getTag()
    {
        return $this->_coreRegistry->registry('tag');
    }

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getPageTitle()
    {
        return $this->getTag()->getId()
            ? __("Edit Tag '%1'", $this->escapeHtml($this->getTag()->getTitle())) : __('New Tag');
    }

    /**
     * Prepare label for tab.
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Tag Details');
    }

    /**
     * Prepare title for tab.
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Tag Details');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
