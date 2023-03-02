<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Setup\Operation\MigrateFeedSchedule;

class ScheduleRegistry
{
    public const SCHEDULE_DATA = 'scheduleData';

    /**
     * @var array
     */
    private $registry = [];

    public function registry($key)
    {
        if (isset($this->registry[$key])) {
            return $this->registry[$key];
        }

        return null;
    }

    public function register($key, $value, $graceful = false)
    {
        if (isset($this->registry[$key])) {
            if ($graceful) {
                return;
            }

            throw new \RuntimeException('Registry key "' . $key . '" already exists');
        }

        $this->registry[$key] = $value;
    }
}
