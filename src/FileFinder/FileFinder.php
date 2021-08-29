<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector\FileFinder;

use Generator;

interface FileFinder
{
    /**
     * @return Generator<string>
     */
    public function getFiles(string ...$directories) : Generator;
}
