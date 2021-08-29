<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector\PhpDocumentor;

use Jerowork\FileClassReflector\ClassReflector;
use Jerowork\FileClassReflector\ClassReflectorFactory;
use Jerowork\FileClassReflector\FileFinder\FileFinder;
use Jerowork\FileClassReflector\FileFinder\RegexIterator\RegexIteratorFileFinder;
use phpDocumentor\Reflection\Php\ProjectFactory as PhpProjectFactory;
use phpDocumentor\Reflection\ProjectFactory;

final class PhpDocumentorClassReflectorFactory implements ClassReflectorFactory
{
    public function __construct(private ProjectFactory $projectFactory, private FileFinder $fileFinder)
    {
    }

    public static function createInstance() : ClassReflector
    {
        return (new self(PhpProjectFactory::createInstance(), new RegexIteratorFileFinder()))->create();
    }

    public function create() : ClassReflector
    {
        return new PhpDocumentorClassReflector($this->projectFactory, $this->fileFinder);
    }
}
