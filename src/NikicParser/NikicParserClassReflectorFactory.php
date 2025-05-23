<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector\NikicParser;

use Jerowork\FileClassReflector\ClassReflector;
use Jerowork\FileClassReflector\ClassReflectorFactory;
use Jerowork\FileClassReflector\FileFinder\FileFinder;
use Jerowork\FileClassReflector\FileFinder\RegexIterator\RegexIteratorFileFinder;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\ParserFactory;

final class NikicParserClassReflectorFactory implements ClassReflectorFactory
{
    public function __construct(
        private readonly FileFinder $fileFinder,
        private readonly Parser $parser,
        private readonly NodeTraverser $nodeTraverser,
    ) {}

    public static function createInstance() : ClassReflector
    {
        return (new self(
            new RegexIteratorFileFinder(),
            (new ParserFactory())->createForNewestSupportedVersion(),
            new NodeTraverser(),
        ))->create();
    }

    public function create() : ClassReflector
    {
        return new NikicParserClassReflector($this->fileFinder, $this->parser, $this->nodeTraverser);
    }
}
