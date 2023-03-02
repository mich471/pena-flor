<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) 2022 Amasty (https://www.amasty.com)
 * @package Product Feed for Magento 2
 */

namespace Amasty\Feed\Model\Indexer;

use Amasty\Feed\Exceptions\LockProcessException;
use Magento\Framework\FlagManager;

class LockManager
{
    public const PROCESS_LOCK_FLAG = 'amasty_feed_process_lock';

    /**
     * @var FlagManager
     */
    private $flagManager;

    public function __construct(
        FlagManager $flagManager
    ) {
        $this->flagManager = $flagManager;
    }

    public function lockProcess(): void
    {
        if ($this->isProcessLocked()) {
            throw new LockProcessException();
        }

        $this->setIsProcessLocked(true);
    }

    public function unlockProcess(): void
    {
        $this->setIsProcessLocked(false);
    }

    public function isProcessLocked(): bool
    {
        return (bool)$this->getFlag(self::PROCESS_LOCK_FLAG);
    }

    private function setIsProcessLocked(bool $value): void
    {
        $this->saveFlag(self::PROCESS_LOCK_FLAG, (string)$value);
    }

    private function getFlag(string $code): string
    {
        return (string)$this->flagManager->getFlagData($code);
    }

    private function saveFlag(string $code, $value): void
    {
        $this->flagManager->saveFlag($code, (string)$value);
    }
}
