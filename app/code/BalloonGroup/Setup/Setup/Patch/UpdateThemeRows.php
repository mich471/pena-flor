<?php

namespace BalloonGroup\Setup\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;

/**
 *
 */
class UpdateThemeRows implements DataPatchInterface
{

    /**
     * @var WriterInterface
     */
    protected WriterInterface $writer;

    /**
     * @param WriterInterface $writer
     */
    public function __construct(WriterInterface $writer)
    {
        $this->writer = $writer;
    }

    /**
     * @return array
     */
    public static function getDependencies(): array
    {
        return [];
    }

    /**
     * @return array
     */
    public function getAliases(): array
    {
        return [];
    }

    /**
     * @return UpdateThemeRows|void
     */
    public function apply()
    {
        $this->writer->delete('design/theme/theme_id'   , 'websites', 3);
        $this->writer->delete('design/theme/theme_id'   , 'websites', 4);
        $this->writer->delete('design/theme/theme_id'   , 'websites', 5);
    }
}
