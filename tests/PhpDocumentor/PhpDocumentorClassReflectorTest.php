<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector\Test\PhpDocumentor;

use Jerowork\FileClassReflector\FileFinder\RegexIterator\RegexIteratorFileFinder;
use Jerowork\FileClassReflector\PhpDocumentor\PhpDocumentorClassReflector;
use Jerowork\FileClassReflector\Test\resources\directory\StubClass3;
use Jerowork\FileClassReflector\Test\resources\directory\sub\StubClass4;
use Jerowork\FileClassReflector\Test\resources\StubClass;
use Jerowork\FileClassReflector\Test\resources\StubClass2;
use phpDocumentor\Reflection\Php\ProjectFactory;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class PhpDocumentorClassReflectorTest extends TestCase
{
    private PhpDocumentorClassReflector $reflector;

    protected function setUp() : void
    {
        $this->reflector = new PhpDocumentorClassReflector(
            ProjectFactory::createInstance(),
            new RegexIteratorFileFinder()
        );
    }

    public function testItShouldAddFiles() : void
    {
        $this->assertEmpty($this->reflector->getFiles());

        $this->reflector->addFile('/path/to/some-file.php');

        $this->assertSame(['/path/to/some-file.php'], $this->reflector->getFiles());

        $this->reflector->addFile('/path/to/another-file.php', 'third-file.php');

        $this->assertSame([
            '/path/to/some-file.php',
            '/path/to/another-file.php',
            'third-file.php',
        ], $this->reflector->getFiles());
    }

    public function testItShouldAddFilesFromDirectories() : void
    {
        $this->assertEmpty($this->reflector->getFiles());

        $this->reflector->addDirectory(__DIR__.'/../resources');

        $this->assertSame([
            __DIR__ . '/../resources/StubClass.php',
            __DIR__ . '/../resources/StubClass2.php',
            __DIR__ . '/../resources/directory/StubClass3.php',
            __DIR__ . '/../resources/directory/sub/StubClass4.php',
        ], $this->reflector->getFiles());
    }

    public function testItShouldReflect() : void
    {
        $this->assertEmpty($this->reflector->getClasses());

        $this->reflector->addDirectory(__DIR__.'/../resources');
        $this->reflector->reflect();

        $this->assertEquals([
            new ReflectionClass(StubClass::class),
            new ReflectionClass(StubClass2::class),
            new ReflectionClass(StubClass3::class),
            new ReflectionClass(StubClass4::class),
        ], $this->reflector->getClasses());
    }
}
