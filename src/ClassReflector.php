<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector;

use ReflectionClass;

interface ClassReflector
{
    public function addFile(string ...$files) : self;

    public function addDirectory(string ...$directories) : self;

    public function reflect() : self;

    /**
     * @return string[]
     */
    public function getFiles() : array;

    /**
     * @return ReflectionClass[]
     */
    public function getClasses() : array;
}
