<?php

/*
 * This file is part of the RouterOS project.
 *
 * (c) Anthonius Munthi <https://itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

declare(strict_types=1);

use RouterOS\Generator\Util\Filesystem;

function filesystem()
{
    $args = func_get_args();

    return new Filesystem($args);
}
