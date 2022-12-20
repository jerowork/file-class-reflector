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
The `ClassReflector` makes use of the [phpdocumentor/reflection](https://github.com/phpDocumentor/Reflection) 
package to retrieve the fully-qualified class name from a file.

Basic usage:

```php
use Jerowork\FileClassReflector\PhpDocumentor\PhpDocumentorClassReflectorFactory;

// Create a new ClassReflector instance directly via a static factory method
$reflector = PhpDocumentorClassReflectorFactory::createInstance();

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
use Jerowork\FileClassReflector\PhpDocumentor\PhpDocumentorClassReflectorFactory;
use Jerowork\FileClassReflector\FileFinder\RegexIterator\RegexIteratorFileFinder;
use phpDocumentor\Reflection\Php\ProjectFactory;

// Create the factory
$factory = new PhpDocumentorClassReflectorFactory(
    ProjectFactory::createInstance(),
    new RegexIteratorFileFinder()
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
use Jerowork\FileClassReflector\PhpDocumentor\PhpDocumentorClassReflectorFactory;
use phpDocumentor\Reflection\Php\ProjectFactory;
use Psr\Container\ContainerInterface;

return [
    ClassReflectorFactory::class => static function (ContainerInterface $container) : ClassReflectorFactory {
        return new PhpDocumentorClassReflectorFactory(
            ProjectFactory::createInstance(),
            $container->get(FileFinder::class)
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
    class: Jerowork\FileClassReflector\PhpDocumentor\PhpDocumentorClassReflectorFactory

  Jerowork\FileClassReflector\FileFinder\FileFinder:
    class: Jerowork\FileClassReflector\FileFinder\RegexIterator\RegexIteratorFileFinder

  phpDocumentor\Reflection\ProjectFactory:
    factory: [phpDocumentor\Reflection\Php\ProjectFactory, 'createInstance']
```
