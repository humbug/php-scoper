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

namespace Humbug\PhpScoper;

use Humbug\PhpScoper\Autoload\Requirements;
use Symfony\Requirements\Requirement;

/**
 * @param string $autoload
 * @param bool $verbose
 *
 * @return bool
 */
function check_requirements($autoload, $verbose)
{
    $lineSize = 70;
    $requirements = new Requirements(dirname(dirname(realpath($autoload))));
    $iniPath = $requirements->getPhpIniPath();

    echo_title('PHP-Scoper Requirements Checker', null, $verbose);

    echo '> PHP is using the following php.ini file:' . PHP_EOL;
    if ($iniPath) {
        echo_style('green', '  ' . $iniPath, $verbose);
    } else {
        echo_style('yellow', '  WARNING: No configuration file (php.ini) used by PHP!', $verbose);
    }

    echo PHP_EOL . PHP_EOL;

    echo '> Checking PHP-Scoper requirements:' . PHP_EOL . '  ';

    $messages = [];

    foreach ($requirements->getRequirements() as $requirement) {
        if ($helpText = get_error_message($requirement, $lineSize)) {
            echo_style('red', 'E', $verbose);
            $messages['error'][] = $helpText;
        } else {
            echo_style('green', '.', $verbose);
        }
    }

    $checkPassed = empty($messages['error']);

    foreach ($requirements->getRecommendations() as $requirement) {
        if ($helpText = get_error_message($requirement, $lineSize)) {
            echo_style('yellow', 'W', $verbose);
            $messages['warning'][] = $helpText;
        } else {
            echo_style('green', '.', $verbose);
        }
    }

    if ($checkPassed) {
        echo_block('success', 'OK', 'Your system is ready to run PHP-Scoper.', $verbose);
    } else {
        echo_block('error', 'ERROR', 'Your system is not ready to run Symfony projects', $verbose);

        echo_title('Fix the following mandatory requirements', 'red', $verbose);

        foreach ($messages['error'] as $helpText) {
            echo ' * ' . $helpText . PHP_EOL;
        }
    }

    if (!empty($messages['warning'])) {
        echo_title('Optional recommendations to improve your setup', 'yellow', $verbose);

        foreach ($messages['warning'] as $helpText) {
            echo ' * ' . $helpText . PHP_EOL;
        }
    }

    return $checkPassed;
}

/**
 * @param Requirement $requirement
 * @param int $lineSize
 * @return string|null
 */
function get_error_message(Requirement $requirement, $lineSize)
{
    if ($requirement->isFulfilled()) {
        return null;
    }

    $errorMessage = wordwrap($requirement->getTestMessage(), $lineSize - 3, PHP_EOL.'   ').PHP_EOL;
    $errorMessage .= '   > '.wordwrap($requirement->getHelpText(), $lineSize - 5, PHP_EOL.'   > ').PHP_EOL;

    return $errorMessage;
}

/**
 * @param string $title
 * @param string|null $style
 * @param bool $verbose
 */
function echo_title($title, $style = null, $verbose)
{
    if (false === $verbose) {
        return;
    }

    $style = $style ?: 'title';

    echo PHP_EOL;
    echo_style($style, $title.PHP_EOL, $verbose);
    echo_style($style, str_repeat('~', strlen($title)).PHP_EOL, $verbose);
    echo PHP_EOL;
}

/**
 * @param string $style
 * @param string $message
 * @param bool $verbose
 */
function echo_style($style, $message, $verbose)
{
    if (false === $verbose) {
        return;
    }

    // ANSI color codes
    $styles = array(
        'reset' => "\033[0m",
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'error' => "\033[37;41m",
        'success' => "\033[37;42m",
        'title' => "\033[34m",
    );
    $supports = has_color_support();

    echo($supports ? $styles[$style] : '').$message.($supports ? $styles['reset'] : '');
}

/**
 * @param string $style
 * @param string $title
 * @param string $message
 * @param bool $verbose
 */
function echo_block($style, $title, $message, $verbose)
{
    if (false === $verbose) {
        return;
    }

    $message = ' '.trim($message).' ';
    $width = strlen($message);

    echo PHP_EOL.PHP_EOL;

    echo_style($style, str_repeat(' ', $width), $verbose);
    echo PHP_EOL;
    echo_style($style, str_pad(' ['.$title.']', $width, ' ', STR_PAD_RIGHT), $verbose);
    echo PHP_EOL;
    echo_style($style, $message, $verbose);
    echo PHP_EOL;
    echo_style($style, str_repeat(' ', $width), $verbose);
    echo PHP_EOL;
}

/**
 * @return bool
 */
function has_color_support()
{
    static $support;

    if (null === $support) {
        if (DIRECTORY_SEPARATOR == '\\') {
            $support = false !== getenv('ANSICON') || 'ON' === getenv('ConEmuANSI');
        } else {
            $support = function_exists('posix_isatty') && @posix_isatty(STDOUT);
        }
    }

    return $support;
}
