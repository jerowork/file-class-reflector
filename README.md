# file-class-reflector
Get fully-qualified classnames based on directory and file paths.

## Installation
Install via [Composer](https://getcomposer.org):
```bash
$ composer require jerowork/file-class-reflector
```

## Usage
The ClassReflector makes use of the [phpdocumentor/reflection](https://github.com/phpDocumentor/Reflection) 
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

## Cache
By default, caching of found classes disabled. This is fine for a development environment.

However, for a production environment you do not want to walk through directories on each request
and reflect classes on it. Therefore you can use any PSR-16 cache implementation.

```php
use Jerowork\FileClassReflector\Cache\Psr16\Psr16CachedClassReflectorDecorator;
use Symfony\Component\Cache\Adapter\ApcuAdapter;
use Symfony\Component\Cache\Psr16Cache;

// ...

// Create a new ClassReflector instance directly via a static factory method
$reflector = PhpDocumentorClassReflectorFactory::createInstance();

// Enable caching with any PSR-16 implementation (e.g. symfony/cache)
$reflector = new Psr16CachedClassReflectorDecorator(
    $reflector,
    new Psr16Cache(new ApcuAdapter())
);

// ...
```

Note: Any PSR-6 cache implementation can be used too, by using Symfony's PSR-6 to PSR-16 adapter.
