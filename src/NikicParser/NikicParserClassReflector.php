<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector\NikicParser;

use Jerowork\FileClassReflector\ClassReflector;
use Jerowork\FileClassReflector\FileFinder\FileFinder;
use PhpParser\NodeTraverser;
use PhpParser\Parser;
use ReflectionClass;

final class NikicParserClassReflector implements ClassReflector
{
    /**
     * @var list<string>
     */
    private array $files = [];

    /**
     * @var list<ReflectionClass>
     */
    private array $classes = [];

    public function __construct(
        private readonly FileFinder $fileFinder,
        private readonly Parser $parser,
        private readonly NodeTraverser $nodeTraverser,
    ) {
    }

    public function addFile(string ...$files) : self
    {
        $this->files = [...$this->files, ...array_values($files)];

        return $this;
    }

    public function addDirectory(string ...$directories) : self
    {
        $this->files = [...$this->files, ...$this->fileFinder->getFiles(...$directories)];

        return $this;
    }

    public function reflect() : self
    {
        $this->nodeTraverser->addVisitor(
            $visitor = new FqcnNodeVisitor(),
        );

        foreach ($this->files as $file) {
            $ast = $this->parser->parse((string) file_get_contents($file));

            if ($ast === null) {
                continue;
            }

            $this->nodeTraverser->traverse($ast);

            $fqcn = $visitor->getFqcn();

            if ($fqcn === null) {
                continue;
            }

            $this->classes[] = new ReflectionClass($fqcn);
        }

        return $this;
    }

    public function getFiles() : array
    {
        return $this->files;
    }

    public function getClasses() : array
    {
        return $this->classes;
    }
}
