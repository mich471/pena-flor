<?php

namespace BalloonGroup\General\Logger;

/** Example:
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $logger = $objectManager->get(\BalloonGroup\General\Logger\Logger::class);
    $logger->info('GDEBUG ' . __FILE__ . ':' . __LINE__ . '/' . __METHOD__);
    $logger->print();
 */

class Logger extends \Monolog\Logger
{

    public function setName()
    {
        $this->name = "general.log";
    }

    public function callTrace()
    {
        $e = new \Exception();
        $trace = explode("\n", $e->getTraceAsString());
        // reverse array to make steps line up chronologically
        $trace = array_reverse($trace);
        array_shift($trace); // remove {main}
        array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = array();
       
        for ($i = 0; $i < $length; $i++)
        {
            $result[] = ($i + 1)  . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
        }
       
        $this->info("\t" . implode("\n\t", $result));
    }

    public function print($var)
    {
        //$this->callTrace();
        $this->info(json_encode($var));


    }
}
