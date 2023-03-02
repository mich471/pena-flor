<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Operation;

use Amasty\Feed\Model\Category\ResourceModel\Taxonomy;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\File\Csv;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\SampleData\Context as SampleDataContext;
use Magento\Framework\Setup\SampleData\FixtureManager;

class UpgradeTo210 implements OperationInterface
{
    public const GOOGLE_CATEGORY = 'googlecategory';
    public const LOCALE_CODE_ID = 1;

    /**
     * @var File
     */
    private $driverFile;

    /**
     * @var Csv
     */
    private $csv;

    /**
     * @var AdapterInterface
     */
    private $connection;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var FixtureManager
     */
    private $fixtureManager;

    public function __construct(
        Csv $csv,
        ResourceConnection $resource,
        File $driverFile,
        SampleDataContext $sampleDataContext
    ) {
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->driverFile = $driverFile;
        $this->csv = $csv;
        $this->fixtureManager = $sampleDataContext->getFixtureManager();
    }

    public function execute(ModuleDataSetupInterface $moduleDataSetup, string $setupVersion): void
    {
        if (version_compare($setupVersion, '2.1.0', '<')) {
            // Workaround for DDL statements are not allowed in transactions
            $this->connection->delete($moduleDataSetup->getTable(Taxonomy::TABLE_NAME));

            $directoryPath = $this->getDirectoryPath();

            if ($this->driverFile->isExists($directoryPath)) {
                $files = $this->driverFile->readDirectory($directoryPath);

                foreach ($files as $file) {
                    if ($this->driverFile->isFile($file)) {
                        $this->getGoogleCategories($file);
                        $this->connection->insertMultiple(
                            $this->resource->getTableName(Taxonomy::TABLE_NAME),
                            $this->getGoogleCategories($file)
                        );
                    }
                }
            }
        }
    }

    /**
     * @return string
     */
    private function getDirectoryPath()
    {
        return $this->fixtureManager->getFixture('Amasty_Feed::fixtures/' . self::GOOGLE_CATEGORY);
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function deleteEmptyItems($data)
    {
        return array_filter($data);
    }

    /**
     * @param string $data
     *
     * @return string|null
     */
    private function getLocaleCode($data)
    {
        $pattern = "/\.([a-z]{2,3}-([A-Za-z]{2,4}-)?[A-Z]{2})\.csv/";
        preg_match_all($pattern, $data, $match);

        return isset($match[self::LOCALE_CODE_ID][0]) ? $match[self::LOCALE_CODE_ID][0] : null;
    }

    /**
     * @param string $file
     *
     * @return array
     */
    private function getGoogleCategories($file)
    {
        $result = [];
        $csvData = $this->csv->getData($file);

        foreach ($csvData as $row => $data) {
            array_shift($data);
            $newData = $this->deleteEmptyItems($data);
            $subcategories = implode(' > ', $newData);
            $languageCode = $this->getLocaleCode($file);

            if ($languageCode) {
                $result[$row] = [
                    'category' => $subcategories,
                    'language_code' => $this->getLocaleCode($file)
                ];
            }
        }

        return $result;
    }
}
