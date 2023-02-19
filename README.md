# file-class-reflector
[![Build Status](https://img.shields.io/endpoint.svg?url=https%3A%2F%2Factions-badge.atrox.dev%2Fjerowork%2Ffile-class-reflector%2Fbadge%3Fref%3Dmain&style=flat-square)](https://github.com/jerowork/file-class-reflector/actions)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/jerowork/file-class-reflector.svg?style=flat-square)](https://scrutinizer-ci.com/g/jerowork/file-class-reflector/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/jerowork/file-class-reflector.svg?style=flat-square)](https://scrutinizer-ci.com/g/jerowork/file-class-reflector)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)
[![Packagist Version](https://img.shields.io/packagist/v/jerowork/file-class-reflector.svg?style=flat-square&include_prereleases)](https://packagist.org/packages/jerowork/file-class-reflector)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-8892BF.svg?style=flat-square)](http://www.php.net)

Get fully-qualified classnames based on directory and file paths.

## Installation
Install via [Composer](https://getcomposer.org):
```bash
composer require jerowork/file-class-reflector
```

## Usage
The `ClassReflector` makes use of the [nikic/php-parser](https://github.com/nikic/php-parser) 
package to retrieve the fully-qualified class name from a file.

Basic usage:

```php
use Jerowork\FileClassReflector\NikicParser\NikicParserClassReflectorFactory;

// Create a new ClassReflector instance directly via a static factory method
$reflector = NikicParserClassReflectorFactory::createInstance();

// Add necessary directories and/or files and reflect
$reflector
    ->addDirectory(__DIR__ . '/some/directory')
    ->reflect();

// Get all \ReflectionClass found in files
$classes = $reflector->getClasses();
```

The `ClassReflectorFactory` can also be instantiated via the constructor.
In this way the factory can be added to a DI container. 
```php
use Jerowork\FileClassReflector\FileFinder\RegexIterator\RegexIteratorFileFinder;
use Jerowork\FileClassReflector\NikicParser\NikicParserClassReflectorFactory;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;

// Create the factory
$factory = new NikicParserClassReflectorFactory(
    new RegexIteratorFileFinder(),
    (new ParserFactory())->create(ParserFactory::PREFER_PHP7),
    new NodeTraverser(),
);

// Create a new ClassReflector instance
$reflector = $factory->create();

// ...
```

### DI service definition
As a good practice we should always 'program to interfaces, not implementations', you should add this to your DI container.

PSR-11 Container example:

```php
use Jerowork\FileClassReflector\ClassReflectorFactory;
use Jerowork\FileClassReflector\FileFinder\FileFinder;
use Jerowork\FileClassReflector\FileFinder\RegexIterator\RegexIteratorFileFinder;
use Jerowork\FileClassReflector\NikicParser\NikicParserClassReflectorFactory;
use PhpParser\NodeTraverser;
use PhpParser\ParserFactory;
use Psr\Container\ContainerInterface;

return [
    ClassReflectorFactory::class => static function (ContainerInterface $container): ClassReflectorFactory {
        return new NikicParserClassReflectorFactory(
            new RegexIteratorFileFinder(),
            (new ParserFactory())->create(ParserFactory::PREFER_PHP7),
            new NodeTraverser(),
        );
    },
    
    FileFinder::class => static fn (): FileFinder => new RegexIteratorFileFinder(),
];
```

Symfony YAML-file example:
```yaml
services:
  _defaults:
    autowire: true
    autoconfigure: true

  Jerowork\FileClassReflector\ClassReflectorFactory:
    class: Jerowork\FileClassReflector\NikicParser\NikicParserClassReflectorFactory

  Jerowork\FileClassReflector\FileFinder\FileFinder:
    class: Jerowork\FileClassReflector\FileFinder\RegexIterator\RegexIteratorFileFinder

  PhpParser\ParserFactory: ~

  PhpParser\Parser:
    factory: ['@PhpParser\ParserFactory', 'create']
    arguments: [1] # 1 = ParserFactory::PREFER_PHP7

  PhpParser\NodeTraverser: ~
```
