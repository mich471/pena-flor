<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Export\Adapter;

use Amasty\Feed\Model\Config;
use Amasty\Feed\Model\Config\Source\NumberFormat;
use Amasty\Feed\Model\Config\Source\StorageFolder;
use Amasty\Feed\Model\OptionSource\Feed\Modifier;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Directory\Model\CurrencyFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Driver\File;
use Magento\Store\Model\StoreManagerInterface;

class Csv extends \Magento\ImportExport\Model\Export\Adapter\Csv
{
    public const HTTP = 'http://';
    public const HTTPS = 'https://';

    /**
     * @var array
     */
    protected $csvField = [];

    /**
     * @var bool
     */
    protected $columnName;

    /**
     * @var string|null
     */
    protected $header;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var array
     */
    protected $rates;

    /**
     * @var string
     */
    protected $formatPriceCurrency;

    /**
     * @var int
     */
    protected $formatPriceCurrencyShow;

    /**
     * @var int
     */
    protected $formatPriceDecimals;

    /**
     * @var string
     */
    protected $formatPriceDecimalPoint;

    /**
     * @var string
     */
    protected $formatPriceThousandsSeparator;

    /**
     * @var string
     */
    protected $formatDate;

    /**
     * @var int|null
     */
    protected $page;

    /**
     * @var NumberFormat
     */
    private $numberFormat;

    /**
     * @var ProductRepositoryInterface
     */
    private $productRepository;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var File
     */
    private $file;

    public function __construct(
        Filesystem $filesystem,
        StoreManagerInterface $storeManager,
        CurrencyFactory $currencyFactory,
        ProductRepositoryInterface $productRepository,
        Escaper $escaper,
        NumberFormat $numberFormat,
        Config $config,
        File $file,
        $destination = null,
        $page = null
    ) {
        $this->storeManager = $storeManager;
        $this->currencyFactory = $currencyFactory;
        $this->productRepository = $productRepository;
        $this->page = $page;
        $this->numberFormat = $numberFormat;
        $this->file = $file;
        $this->filesystem = $filesystem;
        $this->config = $config;

        parent::__construct($filesystem, $destination);
        $this->escaper = $escaper;
    }

    protected function _init()
    {
        return $this;
    }

    public function initBasics($feed)
    {
        $enclosure = $feed->getCsvEnclosure();
        $delimiter = $feed->getCsvDelimiter();

        $enclosures = [
            'double_quote' => '"',
            'quote' => '\'',
            'space' => ' ',
            'none' => '/n'
        ];

        $this->_enclosure = $enclosures[$enclosure] ?? '"';

        $delimiters = [
            'comma' => ',',
            'semicolon' => ';',
            'pipe' => '|',
            //phpcs:ignore
            'tab' => chr(9)
        ];

        $mode = $this->page == 0 ? 'w' : 'a';

        if ($this->config->getStorageFolder() == StorageFolder::VAR_FOLDER) {
            $dir = $this->filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        } else {
            $dir = $this->filesystem->getDirectoryWrite(DirectoryList::MEDIA);
        }
        $directoryPath = $dir->getAbsolutePath($this->config->getFilePath());
        if (!$this->file->isDirectory($directoryPath)) {
            $this->file->createDirectory($directoryPath);
        }
        $this->_directoryHandle = $dir;
        $this->_fileHandler = $dir->openFile($this->_destination, $mode);

        $this->_delimiter = isset($delimiters[$delimiter]) ? $delimiters[$delimiter] : ',';

        $this->columnName = $feed->getCsvColumnName() == 1;

        $this->header = $feed->getCsvHeader();

        $this->csvField = $feed->getCsvField();

        $this->initPrice($feed);

        return $this;
    }

    public function initPrice($feed)
    {
        $decimals = $this->numberFormat->getAllDecimals();
        $separators = $this->numberFormat->getAllSeparators();

        $formatPriceDecimals = $feed->getFormatPriceDecimals();
        $formatPriceDecimalPoint = $feed->getFormatPriceDecimalPoint();
        $formatPriceThousandsSeparator = $feed->getFormatPriceThousandsSeparator();
        $formatDate = $feed->getFormatDate();

        $this->formatPriceCurrency = $feed->getFormatPriceCurrency();
        $this->formatPriceCurrencyShow = $feed->getFormatPriceCurrencyShow() == 1;

        $this->formatPriceDecimals = $decimals[$formatPriceDecimals] ?? 2;
        $this->formatPriceDecimalPoint = $separators[$formatPriceDecimalPoint] ?? '.';

        $this->formatPriceThousandsSeparator = $separators[$formatPriceThousandsSeparator] ?? ',';

        $this->formatDate = !empty($formatDate) ? $formatDate : "Y-m-d";
    }

    protected function _getFieldKey($field)
    {
        $postfix = isset($field['parent']) && $field['parent'] == 'yes' ? '|parent' : '';

        return $field['attribute'] . $postfix;
    }

    public function writeHeader()
    {
        $columns = [];

        foreach ($this->csvField as $idx => $field) {
            $this->_headerCols[$idx . "_idx"] = false;
            $columns[] = $field['header'];
        }

        if (!empty($this->header)) {
            $this->_fileHandler->write($this->header . "\n");
        }

        if ($this->columnName !== false) {
            $this->_fileHandler->writeCsv($columns, $this->_delimiter, $this->_enclosure);
        }

        return $this;
    }

    public function writeFooter()
    {
        return true;
    }

    public function setHeaderCols(array $headerColumns)
    {
        if (null !== $this->_headerCols) {
            throw new LocalizedException(__('The header column names are already set.'));
        }
        if ($headerColumns) {
            foreach ($headerColumns as $columnName) {
                $this->_headerCols[$columnName] = false;
            }
        }

        return $this;
    }

    public function writeDataRow(array &$rowData)
    {
        $writeRow = [];

        foreach ($this->csvField as $idx => $field) {
            if ($field['static_text']) {
                $value = $field['static_text'];
            } else {
                $fieldKey = $this->_getFieldKey($field);
                $value = isset($rowData[$fieldKey]) ? $rowData[$fieldKey] : '';
            }

            $value = $this->_modifyValue($field, $value);
            $value = $this->_formatValue($field, $value);

            $writeRow[$idx . "_idx"] = $value;
        }

        if (count($writeRow) > 0) {
            if ($this->_enclosure == '/n') {
                foreach ($writeRow as $inx => $val) {
                    $writeRow[$inx] = str_replace($this->_delimiter, "", $val);
                }
                $this->_fileHandler->write(implode($this->_delimiter, $writeRow) . "\n");

            } else {
                parent::writeRow($writeRow);
            }
        }

        return $this;
    }

    /**
     * @param array $field
     * @param string $value
     *
     * @return int|float|string
     */
    protected function _modifyValue($field, $value)
    {
        if (isset($field['modify']) && is_array($field['modify'])) {
            foreach ($field['modify'] as $modify) {

                $value = $this->_modify(
                    $value,
                    $modify['modify'],
                    isset($modify['arg0']) ? $modify['arg0'] : null,
                    isset($modify['arg1']) ? $modify['arg1'] : null
                );
            }
        }

        return $value;
    }

    /**
     * @param string $value
     * @param string $modify
     * @param string|null $arg0
     * @param string|null $arg1
     *
     * @return float|int|string
     */
    protected function _modify($value, $modify, $arg0 = null, $arg1 = null)
    {
        switch ($modify) {
            case Modifier::STRIP_TAGS:
                $value = strtr($value, ["\n" => '', "\r" => '']);
                $value = strip_tags($value);
                break;
            case Modifier::GOOGLE_HTML_ESCAPE:
                $value = $this->googleEscapeHtml((string)$value);
                break;
            case Modifier::HTML_ESCAPE:
                $value = $this->escaper->escapeHtml($value);
                break;
            case Modifier::LOWERCASE:
                $value = $this->lowerCase($value);
                break;
            case Modifier::INTEGER:
                $value = (int)$value;
                break;
            case Modifier::LENGTH:
                $length = (int)$arg0;

                if ($arg0 != '') {
                    $value = function_exists("mb_substr")
                        ? mb_substr($value, 0, $length, "UTF-8") : substr($value, 0, $length);
                }
                break;
            case Modifier::PREPEND:
                $value = $arg0 . $value;
                break;
            case Modifier::APPEND:
                $value .= $arg0;
                break;
            case Modifier::REPLACE:
                $value = str_replace($arg0, $arg1, $value);
                break;
            case Modifier::UPPERCASE:
                $value = function_exists("mb_strtoupper")
                    ? mb_strtoupper($value, "UTF-8") : strtoupper($value);
                break;
            case Modifier::CAPITALIZE_FIRST:
                $value = ucfirst($this->lowerCase($value));
                break;
            case Modifier::CAPITALIZE_EACH_WORD:
                $value = ucwords($this->lowerCase($value));
                break;
            case Modifier::ROUND:
                if (is_numeric($value)) {
                    $value = round($value);
                }
                break;
            case Modifier::IF_EMPTY:
                if (!strlen($value)) {
                    $value = $arg0;
                }
                break;
            case Modifier::IF_NOT_EMPTY:
                if (strlen($value)) {
                    $value = $arg0;
                }
                break;
            case Modifier::FULL_IF_NOT_EMPTY:
                if (!strlen($value)) {
                    $value = $arg0;
                } else {
                    $value = $arg1;
                }
                break;

            case Modifier::TO_SECURE_URL:
                $this->replaceFirst($value, self::HTTP, self::HTTPS);
                break;
            case Modifier::TO_UNSECURE_URL:
                $this->replaceFirst($value, self::HTTPS, self::HTTP);
                break;
        }

        return $value;
    }

    private function googleEscapeHtml(string $value): string
    {
        // Remove HTML tags with content
        foreach (['style', 'canvas'] as $tag) {
            $value = preg_replace('/(<' . $tag . '.*?>.*?<\/' . $tag . '>)/is', '', $value);
            $value = preg_replace('/(<' . $tag . ' .*?\/>)/is', '', $value);
        }

        // Remove all attributes from HTML tags
        $value = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/si", '<$1$2>', $value);

        return $this->escaper->escapeHtml(trim($value));
    }

    /**
     * Replace the first occurrence of $first in $value to $replace
     *
     * @param string $value
     * @param string $origin
     * @param string $replace
     */
    private function replaceFirst(&$value, $origin, $replace)
    {
        if (strpos($value, $origin) === 0) {
            $value = substr_replace($value, $replace, 0, strlen($origin));
        }
    }

    /**
     * @param string $value
     *
     * @return string
     */
    private function lowerCase($value)
    {
        return function_exists("mb_strtolower") ? mb_strtolower($value, "UTF-8") : strtolower($value);
    }

    protected function _formatValue($field, $value)
    {
        $format = $field['format'] ?? 'as_is';

        switch ($format) {
            case 'integer':
            case 'as_is':
                break;
            case 'date':
                if (!empty($value)) {
                    $value = date($this->formatDate, strtotime($value));
                }
                break;
            case 'price':
                if (is_numeric($value)) {
                    $value = $value * $this->getCurrencyRate();
                    $value = number_format(
                        $value,
                        $this->formatPriceDecimals,
                        $this->formatPriceDecimalPoint,
                        $this->formatPriceThousandsSeparator
                    );

                    if ($this->formatPriceCurrencyShow && $this->formatPriceCurrency) {
                        $value .= ' ' . $this->formatPriceCurrency;
                    }
                }

                break;
        }

        return $value;
    }

    protected function getCurrencyRate()
    {
        if (!$this->rates) {
            $this->rates = $this->currencyFactory->create()->getCurrencyRates(
                $this->storeManager->getStore()->getBaseCurrency(),
                [$this->formatPriceCurrency]
            );
        }

        return $this->rates[$this->formatPriceCurrency] ?? 1;
    }

    /**
     * Method which caused files deleting on Magento 2.3.5 was redefined
     *
     * @return void
     */
    public function destruct()
    {
        if (is_object($this->_fileHandler)) {
            $this->_fileHandler->close();
        }
    }
}
