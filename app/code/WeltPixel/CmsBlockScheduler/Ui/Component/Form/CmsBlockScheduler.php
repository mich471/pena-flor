<?php
namespace WeltPixel\CmsBlockScheduler\Ui\Component\Form;

/**
 * Install schema
 * @category WeltPixel
 * @package  WeltPixel_CmsBlockScheduler
 * @module   CmsBlockScheduler
 * @author   WeltPixel Developer
 */

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form\FieldFactory;
use Magento\Ui\Component\Form\Fieldset as BaseFieldset;
use WeltPixel\CmsBlockScheduler\Helper\Data;
use WeltPixel\CmsBlockScheduler\Model\ResourceModel\Widget\Instance\Options\Tag;
use Magento\Customer\Ui\Component\Listing\Column\Group\Options;

class CmsBlockScheduler extends BaseFieldset
{
	/**
	 * @var FieldFactory
	 */
	private $fieldFactory;
	private $helper;
	private $tagModel;
	private $customerGroups;
	
	public function __construct(
		ContextInterface $context,
		FieldFactory $fieldFactory,
		Data $helper,
		Tag $tagModel,
		Options $customerGroups,
		array $components = [],
		array $data = []
	)
	{
		parent::__construct($context, $components, $data);
		$this->fieldFactory = $fieldFactory;
		$this->helper = $helper;
		$this->tagModel = $tagModel;
		$this->customerGroups = $customerGroups;
	}
	
	/**
	 * Get components
	 *
	 * @return UiComponentInterface[]
	 */
	public function getChildComponents()
	{
		$tag = $this->helper->resourceEnabled('tag');
		$dateRange = $this->helper->resourceEnabled('date_range');
		$customerGroup = $this->helper->resourceEnabled('customer_group');
		
		$fields = [];
		$fields['tag'] = [
			'dataType'    => 'text',
			'label'       => __('Tag'),
			'formElement' => 'select',
			'options'     => $this->tagModel->toOptionArray(),
			'sortOrder'   => '10',
			'dataScope'   => 'tag',
			'validation'  => [
				'required-entry' => false,
			],
			'default'     => '',
			'disabled'    => !$tag ? true : false,
			'notice'      => !$tag ? __('This feature is disabled. To enable it go to "WeltPixel > CMS Block Scheduler > Configuration" and change setting of "Enable Tag".') : __('Add a custom tag to be easier to find this CMS block in the list.'),
		];
		
		$fields['valid_from'] = [
			'dataType'    => 'string',
			'label'       => __('Valid From'),
			'formElement' => 'date',
			'source'      => 'page',
			'sortOrder'   => '20',
			'dataScope'   => 'valid_from',
			'additionalClasses' => 'admin__field-date',
			'options'     => [
				'showsTime' => true,
			],
			'default'     => '',
			'disabled'    => !$dateRange ? true : false,
			'notice'      => !$dateRange ? __('This feature is disabled. To enable it go to "WeltPixel > CMS Block Scheduler > Configuration" and change setting of "Enable Date Range".') : __('Set the start date and time when this CMS block becomes visible in store front.'),
		];
		$fields['valid_to'] = [
			'dataType'    => 'string',
			'label'       => __('Valid To'),
			'formElement' => 'date',
			'source'      => 'page',
			'sortOrder'   => '30',
			'dataScope'   => 'valid_to',
			'additionalClasses' => 'admin__field-date',
			'options'     => [
				'showsTime' => true,
			],
			'default'     => '',
			'disabled'    => !$dateRange ? true : false,
			'notice'      => !$dateRange ? __('This feature is disabled. To enable it go to "WeltPixel > CMS Block Scheduler > Configuration" and change setting of "Enable Date Range"') : __('Set the end date and time when this CMS block will stop being visible in store front.'),
		];
		
		$fields['customer_group'] = [
			'dataType'    => 'int',
			'label'       => __('Customer Group'),
			'formElement' => 'multiselect',
			'options'     => $this->customerGroups->toOptionArray(),
			'source'      => 'block',
			'sortOrder'   => '40',
			'dataScope'   => 'customer_group',
			'default'     => '',
			'validation'  => [
				'required-entry' => false,
			],
			'disabled'    => !$customerGroup ? true : false,
			'notice'      => !$customerGroup ? __('This feature is disabled. To enable it go to "WeltPixel > CMS Block Scheduler > Configuration" and change setting of "Enable Customer Group"') : __('Target your audience and display this CMS block only for the selected customer group(s).'),
		];
		
		foreach ($fields as $name => $fieldConfig) {
			$fieldInstance = $this->fieldFactory->create();
			$fieldInstance->setData(
				[
					'config' => $fieldConfig,
					'name'   => $name
				]
			);
			
			$fieldInstance->prepare();
			$this->addComponent($name, $fieldInstance);
		}
		
		return parent::getChildComponents();
	}
}