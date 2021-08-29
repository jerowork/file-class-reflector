<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector\Cache\Psr16;

use Jerowork\FileClassReflector\ClassReflector;
use Psr\SimpleCache\CacheInterface;
use ReflectionClass;

final class Psr16CachedClassReflectorDecorator implements ClassReflector
{
    /**
     * @var ReflectionClass[]
     */
    private array $classes = [];

    public function __construct(private ClassReflector $classReflector, private CacheInterface $cache)
    {
    }

    public function addFile(string ...$files) : self
    {
        $this->classReflector->addFile(...$files);

        return $this;
    }

    public function addDirectory(string ...$directories) : self
    {
        $this->classReflector->addDirectory(...$directories);

        return $this;
    }

    public function reflect() : ClassReflector
    {
        $cacheKey   = $this->getCacheKey();
        $cachedData = $this->cache->get($cacheKey);

        if ($cachedData === null) {
            $this->classes = $this->classReflector->reflect()->getClasses();

            $this->cache->set($cacheKey, $this->getPayloadFromClasses($this->classes));

            return $this;
        }

        $this->classes = $this->getClassesFromPayload((string) $cachedData);

        return $this;
    }

    public function getFiles() : array
    {
        return $this->classReflector->getFiles();
    }

    public function getClasses() : array
    {
        return $this->classes;
    }

    private function getCacheKey() : string
    {
        return sha1(implode('', $this->getFiles()));
    }

    /**
     * @param ReflectionClass[] $classes
     */
    private function getPayloadFromClasses(array $classes) : string
    {
        return json_encode(
            array_map(
                static fn (ReflectionClass $reflectionClass) : string => $reflectionClass->getName(),
                $classes
            ),
            JSON_THROW_ON_ERROR
        );
    }

    /**
     * @return ReflectionClass[]
     */
    private function getClassesFromPayload(string $payload) : array
    {
        /** @psalm-suppress ArgumentTypeCoercion */
        return array_map(
            static fn (string $className) : ReflectionClass => new ReflectionClass($className), // @phpstan-ignore-line
            json_decode($payload, true, flags: JSON_THROW_ON_ERROR)
        );
    }
}
