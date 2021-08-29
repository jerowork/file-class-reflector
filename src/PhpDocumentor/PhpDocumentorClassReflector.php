<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector\PhpDocumentor;

use Jerowork\FileClassReflector\ClassReflector;
use Jerowork\FileClassReflector\FileFinder\FileFinder;
use phpDocumentor\Reflection\File;
use phpDocumentor\Reflection\File\LocalFile;
use phpDocumentor\Reflection\Php\Class_;
use phpDocumentor\Reflection\Php\Project;
use phpDocumentor\Reflection\ProjectFactory;
use ReflectionClass;

final class PhpDocumentorClassReflector implements ClassReflector
{
    /**
     * @var string[]
     */
    private array $files = [];

    /**
     * @var ReflectionClass[]
     */
    private array $classes = [];

    public function __construct(private ProjectFactory $projectFactory, private FileFinder $fileFinder)
    {
    }

    public function addFile(string ...$files) : self
    {
        /** @psalm-suppress DuplicateArrayKey */
        $this->files = [...$this->files, ...$files];

        return $this;
    }

    public function addDirectory(string ...$directories) : self
    {
        /** @psalm-suppress DuplicateArrayKey */
        $this->files = [...$this->files, ...$this->fileFinder->getFiles(...$directories)];

        return $this;
    }

    public function reflect() : self
    {
        /** @var Project $project */
        $project = $this->projectFactory->create(self::class, array_map(
            static fn (string $file) : File => new LocalFile($file),
            $this->files
        ));

        $classes = [];
        foreach ($project->getFiles() as $file) {
            $classes = [
                ...$classes,
                ...array_values(array_map(
                    static fn (Class_ $class) : string => (string) $class->getFqsen(),
                    $file->getClasses()
                )),
            ];
        }

        /** @psalm-suppress ArgumentTypeCoercion */
        $this->classes = array_map(
            static fn (string $class) : ReflectionClass => new ReflectionClass($class), // @phpstan-ignore-line
            array_unique($classes)
        );

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
