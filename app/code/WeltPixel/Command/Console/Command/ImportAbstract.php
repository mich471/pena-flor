<?php
namespace WeltPixel\Command\Console\Command;

use Magento\Setup\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use \Magento\Framework\File\Csv;
use \Magento\Config\Model\ResourceModel\Config;
use \Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Component\ComponentRegistrarInterface;

class ImportAbstract extends Command
{
    /**
     * @var Csv
     */
    protected $csvFile;

    /**
     * @var Config
     */
    protected $resourceConfig;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ComponentRegistrarInterface
     */
    protected $componentRegistrar;

    /**
     * @var \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory
     */
    protected $themeFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Serialize
     */
    protected $serializer;


    /**
     * ExportConfigurationsCommand constructor.
     * @param Csv $csvFile
     * @param Config $resourceConfig
     * @param StoreManagerInterface $storeManager
     * @param ComponentRegistrarInterface $componentRegistrar
     * @param \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $themeFactory
     * @param \Magento\Framework\Serialize\Serializer\Serialize $serializer
     */
    public function __construct(
        Csv $csvFile,
        Config $resourceConfig,
        StoreManagerInterface $storeManager,
        ComponentRegistrarInterface $componentRegistrar,
        \Magento\Theme\Model\ResourceModel\Theme\CollectionFactory $themeFactory,
        \Magento\Framework\Serialize\Serializer\Serialize $serializer
    )
    {
        $this->csvFile = $csvFile;
        $this->resourceConfig = $resourceConfig;
        $this->storeManager = $storeManager;
        $this->componentRegistrar = $componentRegistrar;
        $this->themeFactory = $themeFactory;
        $this->serializer = $serializer;
        parent::__construct();
    }

    /**
     * @param array $csvData
     * @param \Magento\Store\Api\Data\StoreInterface $store
     */
    protected function importCsvData($csvData, $store)
    {
        $storeId = $store->getId();
        foreach ($csvData as $data) {
            if (!isset($data[0])) {
                continue;
            }
            $scopeId = $storeId;
            /** If GLOBAL store was used we import in DEFAULT SCOPE */
            if ($storeId != 0) {
                if ($data[2] == \Magento\Framework\App\Config::SCOPE_TYPE_DEFAULT) {
                    $scopeId = 0;
                } elseif ($data[2] == \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITES) {
                    $scopeId = $store->getWebsiteId();
                }
            } else {
                $data[2] = \Magento\Framework\App\Config::SCOPE_TYPE_DEFAULT;
            }

            switch ($data[0]) {
                case 'weltpixel_custom_header/search_options/border_width':
                case 'weltpixel_category_page/toolbar/select_border_width':
                    try {
                        $unserialized = $this->serializer->unserialize($data[1]);
                        $data[1] = json_encode($unserialized);
                    } catch (\Exception $ex) {
                    }
                    break;
            }

            $this->resourceConfig->saveConfig($data[0], $data[1], $data[2], $scopeId);
        }
    }
}
