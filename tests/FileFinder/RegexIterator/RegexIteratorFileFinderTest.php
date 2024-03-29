<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector\Test\FileFinder\RegexIterator;

use Jerowork\FileClassReflector\FileFinder\RegexIterator\RegexIteratorFileFinder;
use PHPUnit\Framework\TestCase;

final class RegexIteratorFileFinderTest extends TestCase
{
    public function testItShouldFindPhpFiles() : void
    {
        $finder = new RegexIteratorFileFinder();

        $files = $finder->getFiles(__DIR__ . '/../../resources');

        $this->assertSame(
            [
                __DIR__ . '/../../resources/ClassWithoutNamespace.php',
                __DIR__ . '/../../resources/StubClass.php',
                __DIR__ . '/../../resources/StubClass2.php',
                __DIR__ . '/../../resources/StubEnum.php',
                __DIR__ . '/../../resources/StubInterface.php',
                __DIR__ . '/../../resources/StubTrait.php',
                __DIR__ . '/../../resources/directory/StubClass3.php',
                __DIR__ . '/../../resources/directory/sub/StubClass4.php',
            ],
            $files
        );
    }
}
