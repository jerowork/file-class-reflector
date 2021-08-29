<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector\FileFinder\RegexIterator;

use Generator;
use Jerowork\FileClassReflector\FileFinder\FileFinder;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RegexIterator;

final class RegexIteratorFileFinder implements FileFinder
{
    private const REGEX_PHP_FILE = '/^.+\.php$/i';

    public function getFiles(string ...$directories) : Generator
    {
        foreach ($directories as $directory) {
            $filesIterator = new RegexIterator(
                new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory)),
                self::REGEX_PHP_FILE,
                RegexIterator::GET_MATCH
            );

            foreach ($filesIterator as $filePath) {
                if (isset($filePath[0]) === false) {
                    continue;
                }

                yield $filePath[0];
            }
        }
    }
}
