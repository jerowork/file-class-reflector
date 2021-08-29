<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector\FileFinder;

interface FileFinder
{
    /**
     * @return string[]
     */
    public function getFiles(string ...$directories) : array;
}
