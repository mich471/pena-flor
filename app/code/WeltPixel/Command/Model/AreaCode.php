<?php
namespace WeltPixel\Command\Model;

class AreaCode
{
    /**
     * @var \Magento\Framework\App\State
     */
    protected $state;

    /**
     * @param \Magento\Framework\App\State $state
     */
    public function __construct(
        \Magento\Framework\App\State $state
    ) {
        $this->state = $state;
    }

    public function setAreaCode()
    {
        $areaCode = null;
        try {
            $areaCode = $this->state->getAreaCode();
        } catch (\Exception $ex) {
        }
        try {
            if (!$areaCode) {
                $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML);
            }
        } catch (\Exception $ex) {
        }
    }
}
