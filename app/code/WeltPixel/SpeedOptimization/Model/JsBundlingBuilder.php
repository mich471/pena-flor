<?php
namespace WeltPixel\SpeedOptimization\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\UrlInterface;
use Psr\Log\LoggerInterface;
use WeltPixel\SpeedOptimization\Helper\Data as WpSpeedHelper;
use WeltPixel\SpeedOptimization\Model\ResourceModel\JsBundling\CollectionFactory as JsBundlingCollectionFactory;


/**
 * Class JsBundlingBuilder
 * @package WeltPixel\SpeedOptimization\Model
 */
class JsBundlingBuilder
{

    /**
     * @var string
     */
    protected $jsbundlingTableName;

    /**
     * @var AdapterInterface
     */
    protected $connection;

    /**
     * @var ResourceConnection
     */
    protected $resource;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var JsBundlingCollectionFactory
     */
    protected $jsBundlingCollectionFactory;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var SessionManagerInterface
     */
    protected $coreSession;

    /**
     * @var WpSpeedHelper
     */
    protected $wpSpeedHelper;

    /**
     * @param ResourceConnection $resource
     * @param LoggerInterface $logger
     * @param JsBundlingCollectionFactory $jsBundlingCollectionFactory
     * @param Filesystem $filesystem
     * @param UrlInterface $urlBuilder
     * @param SessionManagerInterface $coreSession
     * @param WpSpeedHelper $wpSpeedHelper
     */
    public function __construct(
        ResourceConnection $resource,
        LoggerInterface $logger,
        JsBundlingCollectionFactory $jsBundlingCollectionFactory,
        Filesystem $filesystem,
        UrlInterface $urlBuilder,
        SessionManagerInterface $coreSession,
        WpSpeedHelper $wpSpeedHelper
    )
    {
        $this->resource = $resource;
        $this->connection = $resource->getConnection();
        $this->logger = $logger;
        $this->jsBundlingCollectionFactory = $jsBundlingCollectionFactory;
        $this->filesystem = $filesystem;
        $this->urlBuilder = $urlBuilder;
        $this->coreSession = $coreSession;
        $this->wpSpeedHelper = $wpSpeedHelper;
        $this->jsbundlingTableName = 'weltpixel_speedoptimization_jsbundling';
    }

    /**
     * @return string
     */
    public function getJsBunldingTableName()
    {
        return $this->jsbundlingTableName;
    }

    /**
     * @param string $themePath
     * @param array $configOptions
     */
    public function parseBundlingConfigOptions($themePath, $configOptions) {
        $this->_startJsBundlingConfig($themePath);
        foreach ($configOptions as $configKey => $options) {
            if (isset($configOptions) && isset($options)) {
                $this->_addJsBundlingConfig($themePath, $configKey, $options);
            }
        }
    }

    /**
     * @param $themePath
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     *
     */
    public function generateAdvancedBundlingJs($themePath) {
        $jsBundlingValues = $this->_getInitialValues();
        $jsFiles = $this->_getJsFilesForTheme($themePath);
        $modulesIncludes = [];
        foreach ($jsFiles as $optionName => $options) {
            if (!count($options)) continue;
            switch ($optionName) {
                case "paths":
                    $optionsToSave = $options[0];
                    $optionsToSave = str_replace(['mage/requirejs/text'], ['requirejs/text'], $optionsToSave);
                    $jsBundlingValues[$optionName] = json_decode($optionsToSave, true);
                    break;
                case "shim" :
                case "map":
                $jsBundlingValues[$optionName] = json_decode($options[0], true);
                    break;
                default:
                    $modulesIncludes[$optionName] = $options;
                    break;
            }
        }
        $jsBundlingValues['modules'] = $this->_getBundlingModules($modulesIncludes);

        $jsBundlingExportDir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        $jsBundlingFileName =  'requirejsBundling' . DIRECTORY_SEPARATOR .'advancedbundling_build_' . str_replace("/", "_", $themePath) . '.js';
        $jsFileContent = "(" . str_replace('"true"', 'true', json_encode($jsBundlingValues, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) ) . ");";

        try {
            $jsBundlingExportDir->writeFile($jsBundlingFileName, $jsFileContent);
        } catch (\Exception $ex) {
            $this->logger->alert("WeltPixel SpeedOptimization JsBundling File Generation: " . $ex->getMessage());
            return false;
        }

        $urlType = ['_type' => UrlInterface::URL_TYPE_MEDIA];
        $this->coreSession->setJsBundlingFile($jsBundlingFileName);
        return $this->urlBuilder->getBaseUrl($urlType) . $jsBundlingFileName;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getJsBunldingFileContent() {
        $jsBundlingExportDir = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $jsBundlingFileName = $this->coreSession->getJsBundlingFile();
        return $jsBundlingExportDir->readFile($jsBundlingFileName);
    }

    /**
     * @return string
     */
    public function getJsBunldingFileName() {
        $jsBundlingFileName = $this->coreSession->getJsBundlingFile();
        return basename($jsBundlingFileName);
    }

    /**
     * @param string $themePath
     * @param string $pageIdentifier
     * @param array $modules
     */
    public function parseBundlingPageModules($themePath, $pageIdentifier, $modules) {
        if (isset($themePath) && isset($pageIdentifier) && count($modules)) {
            $jsBundlingsForPageCollection = $this->jsBundlingCollectionFactory->create()
                ->addFieldToFilter('themePath', $themePath)
                ->addFieldToFilter('bundling_identifier', $pageIdentifier);

            $ignorejsBundlings = [];
            foreach ($jsBundlingsForPageCollection as $jsBundling) {
                $ignorejsBundlings[] = $jsBundling->getData('requiredfields');
            }

            $bundlingsToInsert = array_diff($modules, $ignorejsBundlings);

            $rows = [];
            foreach ($bundlingsToInsert as $bundlingName) {
                $rows[] = [
                    'themePath' => $themePath,
                    'bundling_identifier' => $pageIdentifier,
                    'requiredfields' => $bundlingName
                ];
            }
            if (!empty($rows)) {
                $jsBundlingTableName = $this->getJsBunldingTableName();
                try {
                    $this->connection->insertMultiple($this->resource->getTableName($jsBundlingTableName), $rows);
                } catch (\Exception $ex) {
                    $this->logger->alert("WeltPixel SpeedOptimization JsBundling: " . $ex->getMessage());
                }
            }
        }
    }

    /**
     * @param string $themePath
     */
    protected function _startJsBundlingConfig($themePath) {
        $jsBundlingTableName = $this->getJsBunldingTableName();
        try {
            $this->connection->delete(
                $this->resource->getTableName($jsBundlingTableName),
                [
                    $this->connection->quoteInto('themePath = ?', $themePath)
                ]
            );
        } catch (\Exception $ex) {
            $this->logger->alert("WeltPixel SpeedOptimization JsBundling: " . $ex->getMessage());
        }
    }

    /**
     * @param string $themePath
     * @param string $configKey
     * @param string $options
     */
    protected function _addJsBundlingConfig($themePath, $configKey, $options) {
        $jsBundlingTableName = $this->getJsBunldingTableName();
        try {
            $this->connection->delete(
                $this->resource->getTableName($jsBundlingTableName),
                [
                    $this->connection->quoteInto('themePath = ?', $themePath),
                    $this->connection->quoteInto('bundling_identifier = ?', $configKey)
                ]
            );

            $row = [
                'themePath' => $themePath,
                'bundling_identifier' => $configKey,
                'requiredfields' => $options
            ];
            $this->connection->insert($this->resource->getTableName($jsBundlingTableName), $row);
        } catch (\Exception $ex) {
            $this->logger->alert("WeltPixel SpeedOptimization JsBundling: " . $ex->getMessage());
        }
    }

    /**
     * @param string $themePath
     * @return array
     */
    protected function _getJsFilesForTheme($themePath) {
        $result = [];
        $jsbundlingCollection = $this->jsBundlingCollectionFactory->create()
            ->addFieldToFilter('themepath', $themePath);

        if ($jsbundlingCollection->getSize()) {
            foreach ($jsbundlingCollection as $files) {
                $result[$files['bundling_identifier']][] = $files['requiredfields'];
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function _getInitialValues() {
        $jsOptimizationMethodValue = $this->wpSpeedHelper->getJsOptimizationMethod();
        $jsOptimization = "uglify2";
        if ($jsOptimizationMethodValue == \WeltPixel\SpeedOptimization\Model\Config\Source\Optimization::OPTIMIZATION_NONE) {
            $jsOptimization = "none";
        }

        return [
            "optimize" => $jsOptimization,
            "generateSourceMaps" => "true",
            "wrapShim" => "true",
            "inlineText" => "true"
        ];
    }

    /**
     * @param array $modulesIncludes
     * @return array
     */
    protected function _getBundlingModules($modulesIncludes) {
        $modulesConfig = [];
        $sharedFiles = [];
        foreach ($modulesIncludes as $bundleKey => $options) {
            if (empty($sharedFiles)) {
                $sharedFiles = $options;
            } else {
                $sharedFiles = (array_intersect( $sharedFiles, $options));
            }
        }

        $modulesConfig[] = [
            "name" => "bundles/shared",
            "create" => "true",
            "exclude" => [],
            "include" => array_values($sharedFiles)
        ];


        foreach ($modulesIncludes as $bundleKey => $options) {
           $includedModules = array_diff($options, $sharedFiles);
           if (!count($includedModules)) continue;
            $modulesConfig[] = [
                "name" => "bundles/" . str_replace("_", "-", $bundleKey),
                "create" => "true",
                "exclude" => [
                    "bundles/shared"
                ],
                "include" => array_values($includedModules)
            ];
        }

        return $modulesConfig;
    }
}
