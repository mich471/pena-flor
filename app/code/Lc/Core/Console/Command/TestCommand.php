<?php

namespace Lc\Core\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\App\State;
use Magento\Store\Model\StoresConfig;
use Magento\Eav\Model\Config;

class TestCommand extends Command
{
   
    /**
     * @var StoresConfig
     */
    protected $storesConfig;

    /**
    * @var State
    */
    private $state;

    /**
    * @var Config
    */
    private $eavConfig;

    /**
     * @param State $state
     * @param StoresConfig $storesConfig
     */
    public function __construct(
        State $state,
        StoresConfig $storesConfig,
        Config $eavConfig
    ) {
        parent::__construct();
        $this->storesConfig = $storesConfig;
        $this->state = $state;
        $this->eavConfig = $eavConfig;
//        $state->setAreaCode('frontend');
    }

    /**
     * @inheritdoc
     */
    protected function configure()
    {
        $this->setName('lc:core:test')
            ->setDescription('Test Command');
    }


    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln("<info>Starting</info>");
        $attribute = $this->eavConfig->getAttribute('customer_address', 'postcode');
        // $output->writeln("data: ". print_r($attribute->getData(), true));
        $output->writeln("data: ". print_r($attribute->getValidateRules(), true));
        // $attribute->setValidateRules([
            // 'min_text_length' => 1,
            // 'max_text_length' => 20
        // ]);
        // $attribute->save();
        // $output->writeln("data: ". print_r($attribute->getValidateRules(), true));
        $output->writeln("<info>Finished</info>");
    }

}
