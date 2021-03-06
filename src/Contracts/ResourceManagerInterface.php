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

namespace RouterOS\Generator\Contracts;

use RouterOS\Generator\Structure\ResourceStructure;

interface ResourceManagerInterface
{
    /**
     * @param string $name;
     *
     * @return ResourceStructure
     */
    public function getResource(string $name): ResourceStructure;

    /**
     * Returns lists submenu with name and id
     * return array.
     */
    public function getList(): array;
}
