<?php
namespace WeltPixel\Command\Model\Console;

use Symfony\Component\Console;

class Cli extends \Magento\Framework\Console\Cli
{
    /**
     * @inheritdoc
     *
     * @throws \Exception The exception in case of unexpected error
     */
    public function doRun(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $areaCode = $objectManager->get('WeltPixel\Command\Model\AreaCode');
        $areaCode->setAreaCode();
        $exitCode = parent::doRun($input, $output);

        return $exitCode;
    }
}
