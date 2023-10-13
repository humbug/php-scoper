<?php

declare(strict_types=1);

/*
 * This file is part of the humbug/php-scoper package.
 *
 * Copyright (c) 2017 Théo FIDRY <theo.fidry@gmail.com>,
 *                    Pádraic Brady <padraic.brady@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Humbug\PhpScoperComposerRootChecker;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Stringable;
use function Safe\file_put_contents;
use const PHP_EOL;

final class Logger extends AbstractLogger
{
    private array $recordsWithLevel = [];
    private array $noticeRecords = [];
    private bool $hasError = false;

    public function __destruct()
    {
        $this->writeRecords();
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $record = sprintf('[%s] %s', $level, $message).PHP_EOL;

        if ($level === LogLevel::ERROR) {
            $this->hasError = true;
            $this->recordsWithLevel[] = [$level, $record];
        } elseif ($level === LogLevel::NOTICE) {
            $this->noticeRecords[] = $record;
        } else {
            $this->recordsWithLevel[] = [$level, $record];
        }
    }

    private function writeRecords(): void
    {
        if (!$this->hasError) {
            foreach ($this->noticeRecords as $noticeRecord) {
                echo $noticeRecord;
            }

            return;
        }

        foreach ($this->recordsWithLevel as [$level, $record]) {
            if ($level === LogLevel::ERROR) {
                file_put_contents(
                    'php://stderr',
                    $record,
                );
            } else {
                echo $record;
            }
        }
    }
}
