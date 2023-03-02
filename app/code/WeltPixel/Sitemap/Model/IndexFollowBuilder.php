<?php
namespace WeltPixel\Sitemap\Model;

/**
 * Class Sitemap
 * @package WeltPixel\Sitemap\Model
 */
class IndexFollowBuilder
{

    /**
     * @var \WeltPixel\Sitemap\Model\Attribute\Source\IndexValue $indexValueSource
     */
    protected $indexValueSource;

    /**
     * @var \WeltPixel\Sitemap\Model\Attribute\Source\FollowValue $followValueSource
     */
    protected $followValueSource;

    /**
     * @var array
     */
    protected $followOptions = null;

    /**
     * @var array
     */
    protected $indexOptions = null;


    /**
     * Constructor
     *
     * @param \WeltPixel\Sitemap\Model\Attribute\Source\IndexValue $indexValueSource
     * @param \WeltPixel\Sitemap\Model\Attribute\Source\FollowValue $followValueSource
     */
    public function __construct(
        \WeltPixel\Sitemap\Model\Attribute\Source\IndexValue $indexValueSource,
        \WeltPixel\Sitemap\Model\Attribute\Source\FollowValue $followValueSource
    ) {
        $this->followValueSource = $followValueSource;
        $this->indexValueSource = $indexValueSource;
    }

    protected function _init()
    {
        if (is_null($this->followOptions)) {
            $this->followOptions = $this->followValueSource->getAvailableOptions();
        }

        if (is_null($this->indexOptions)) {
            $this->indexOptions = $this->indexValueSource->getAvailableOptions();
        }
    }

    /**
     * @param integer $indexValue
     * @param integer $followValue
     * @return string
     */
    public function getIndexFollowValue($indexValue, $followValue)
    {
        $this->_init();
        return  $this->indexOptions[$indexValue] . ',' . $this->followOptions[$followValue];
    }
}
