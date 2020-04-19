<?php
declare(strict_types=1);

/*
 * This file is part of the "jobrouter_process" extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace Brotkrueml\JobRouterProcess;

/**
 * @internal
 */
final class Extension
{
    public const KEY = 'jobrouter_process';

    public const LANGUAGE_PATH_DASHBOARD = 'LLL:EXT:' . self::KEY . '/Resources/Private/Language/Dashboard.xlf';
}
