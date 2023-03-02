<?php
/**
 * Magento 2 Lyracons Core
 * Copyright (C) 2019  Lyracons
 *
 * @author Lyracons DevTeam <devteam@lyracons.com>
 */
namespace Lyracons\Icommkt\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger as MonologLogger;

class Logger extends Base
{

    const FILE_NAME = '/var/log/lyracons-icommkt.log';

    /**
     * @var string
     */
    protected $fileName = self::FILE_NAME;

    /**
     * @var
     */
    protected $loggerType = MonologLogger::DEBUG;

}
