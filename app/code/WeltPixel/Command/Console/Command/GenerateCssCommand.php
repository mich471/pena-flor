<?php
namespace WeltPixel\Command\Console\Command;

use Magento\Directory\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Store\Model\StoreManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use WeltPixel\Command\Model\AreaCode;

class GenerateCssCommand extends Command
{
    const ARGUMENT_STORE = 'store';

    /**
     * @var \WeltPixel\Command\Model\GenerateCss
     */
    protected $generateCss;

    /**
     * Object Manager
     *
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /** @var  ThemeProviderInterface */
    protected $themeProvider;

    /** @var  string */
    protected $locale;

    /** @var  string */
    protected $themePath;

    /**
     * @var \WeltPixel\Backend\Helper\Utility
     */
    protected $utilityHelper;

    /**
     * @var AreaCode
     */
    protected $areaCode;

    /**
     * GenerateCssCommand constructor.
     * @param AreaCode $areaCode
     * @param \WeltPixel\Command\Model\GenerateCss $generateCss
     * @param ObjectManagerInterface $objectManager
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param ThemeProviderInterface $themeProvider
     * @param \WeltPixel\Backend\Helper\Utility $utilityHelper
     */
    public function __construct(
        AreaCode $areaCode,
        \WeltPixel\Command\Model\GenerateCss $generateCss,
        ObjectManagerInterface $objectManager,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        ThemeProviderInterface $themeProvider,
        \WeltPixel\Backend\Helper\Utility $utilityHelper
    ) {
        $this->generateCss = $generateCss;
        $this->objectManager = $objectManager;
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->themeProvider = $themeProvider;
        $this->utilityHelper = $utilityHelper;
        $this->areaCode = $areaCode;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('weltpixel:css:generate')
            ->setDescription('Regenerate the css files (styles-m, styles-l and module store specific css) based on less changes')
            ->setDefinition([
                new InputOption(
                    self::ARGUMENT_STORE,
                    null,
                    InputOption::VALUE_REQUIRED,
                    'Store'
                )
            ]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->areaCode->setAreaCode();
        /** @var  $storeCode */
        $storeCode = $input->getOption(self::ARGUMENT_STORE);

        if ($storeCode == 'all') {
            $storeCollection = $this->storeManager->getStores();
            foreach ($storeCollection as $store) {
                $storeCode = $store->getData('code');
                try {
                    $this->validateInputs($storeCode);

                    $isPearlTheme = $this->utilityHelper->isPearlThemeUsed($storeCode);
                    $output->writeln("Css generation started for store: " . $storeCode);
                    if ($isPearlTheme) {
                        $this->generateCss->processContent($this->themePath, $this->locale, $storeCode);
                    } else {
                        $output->writeln("<error>It only works with pearl theme or it's subchilds.</error>");
                    }
                    $output->writeln("Css generation ended for store: " . $storeCode);
                } catch (\Exception $ex) {
                    $output->writeln("<error>" . $ex->getMessage() . ' : ' . $storeCode . "</error>");
                }
            }
        } else {
            $this->validateInputs($storeCode);

            $isPearlTheme = $this->utilityHelper->isPearlThemeUsed($storeCode);
            if ($isPearlTheme) {
                $output->writeln("Css generation started.");
                $this->generateCss->processContent($this->themePath, $this->locale, $storeCode);
                $output->writeln("Css generation ended.");
            } else {
                $output->writeln("<error>It only works with pearl theme or it's subchilds.</error>");
            }
        }
    }

    /**
     * @param string $storeCode
     * @return array
     * @throws \Exception
     */
    protected function validateInputs($storeCode)
    {
        if (is_null($storeCode) || !trim($storeCode)) {
            throw new \InvalidArgumentException('Argument ' . self::ARGUMENT_STORE . ' is missing.');
        }

        try {
            $store = $this->storeManager->getStore($storeCode);
            $this->locale = $this->scopeConfig->getValue(
                Data::XML_PATH_DEFAULT_LOCALE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store->getId()
            );

            $themeId = $this->scopeConfig->getValue(
                \Magento\Framework\View\DesignInterface::XML_PATH_THEME_ID,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store->getId()
            );

            $theme = $this->themeProvider->getThemeById($themeId);
            $this->themePath = $theme->getThemePath();
        } catch (\Exception $ex) {
            throw new \Exception('Store with id or code ' . $storeCode . ' not found.');
        }
    }
}
