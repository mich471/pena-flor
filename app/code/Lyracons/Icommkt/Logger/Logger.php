<?php
/**
 * Magento 2 Lyracons Core
 * Copyright (C) 2019  Lyracons
 *
 * @author Lyracons DevTeam <devteam@lyracons.com>
 */
namespace Lyracons\Icommkt\Logger;

use Monolog\Logger as MonologLogger;

class Logger extends MonologLogger
{
    /**
     * @var boolean
     */
    private $enabled;

    /**
     * @var boolean
     */
    private $verboseMode;

    /**
     * Adds a log record at the DEBUG level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string $message The log message
     * @param  array $context The log context
     * @return bool   Whether the record has been processed
     */
    public function debug($message, array $context = array())
    {
        if ($this->isEnabled()) {
            return parent::debug($message, $context);
        }
        return false;
    }

    /**
     * Adds a log record at the DEBUG level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string $message The log message
     * @param  array $context The log context
     * @return bool   Whether the record has been processed
     */
    public function verbose($message, array $context = array())
    {
        if ($this->isEnabled() && $this->isVerboseMode()) {
            return parent::debug($message, $context);
        }
        return false;
    }

    /**
     * Adds a log record at the INFO level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string $message The log message
     * @param  array $context The log context
     * @return bool   Whether the record has been processed
     */
    public function info($message, array $context = array())
    {
        if ($this->isEnabled()) {
            return parent::info($message, $context);
        }
        return false;
    }

    /**
     * Adds a log record at the NOTICE level.
     *
     * This method allows for compatibility with common interfaces.
     *
     * @param  string $message The log message
     * @param  array $context The log context
     * @return bool   Whether the record has been processed
     */
    public function notice($message, array $context = array())
    {
        if ($this->isEnabled()) {
            return parent::notice($message, $context);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * @param bool $enabled
     * @return Logger
     */
    public function setEnabled(bool $enabled): Logger
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * @return bool
     */
    public function isVerboseMode(): bool
    {
        return $this->verboseMode;
    }

    /**
     * @param bool $mode
     * @return Logger
     */
    public function setVerboseMode(bool $mode): Logger
    {
        $this->verboseMode = $mode;
        return $this;
    }
}
