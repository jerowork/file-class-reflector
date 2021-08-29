<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector;

interface ClassReflectorFactory
{
    public function create() : ClassReflector;
}
