<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector\Test\Cache\Psr16;

use Jerowork\FileClassReflector\Cache\Psr16\Psr16CachedClassReflectorDecorator;
use Jerowork\FileClassReflector\ClassReflector;
use Jerowork\FileClassReflector\Test\resources\directory\StubClass3;
use Jerowork\FileClassReflector\Test\resources\StubClass2;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use Psr\SimpleCache\CacheInterface;
use ReflectionClass;

final class Psr16CachedClassReflectorDecoratorTest extends MockeryTestCase
{
    /**
     * @var MockInterface&ClassReflector
     */
    private MockInterface $classReflector;

    /**
     * @var MockInterface&CacheInterface
     */
    private MockInterface $cache;

    private Psr16CachedClassReflectorDecorator $decorator;

    protected function setUp() : void
    {
        $this->decorator = new Psr16CachedClassReflectorDecorator(
            $this->classReflector = Mockery::mock(ClassReflector::class),
            $this->cache = Mockery::mock(CacheInterface::class)
        );
    }

    public function testItShouldGetClasses() : void
    {
        $this->classReflector->expects('addDirectory')->with('/directory/a', '/directory/b');

        $this->decorator->addDirectory('/directory/a', '/directory/b');

        $this->classReflector->allows('getFiles')->andReturn([
            __DIR__ . '/../../resources/directory/StubClass3.php',
            __DIR__ . '/../../resources/StubClass2.php',
        ]);

        // Get from directories for first time
        $this->cache->expects('get')->with('15219da6d0b9436989d2f14473289556fa4c2e31')->andReturnNull();

        $this->classReflector->expects('reflect')->andReturn($this->classReflector);
        $this->classReflector->expects('getClasses')->andReturn([
            new ReflectionClass(StubClass2::class),
            new ReflectionClass(StubClass3::class),
        ]);

        // Save to cache for first time
        $this->cache->expects('set')->with(
            '15219da6d0b9436989d2f14473289556fa4c2e31',
            json_encode([StubClass2::class,  StubClass3::class])
        );

        $this->decorator->reflect();

        $classes = $this->decorator->getClasses();

        $this->assertEquals([
            new ReflectionClass(StubClass2::class),
            new ReflectionClass(StubClass3::class),
        ], $classes);

        // Get from cache for second time
        $this->cache->expects('get')->with('15219da6d0b9436989d2f14473289556fa4c2e31')->andReturn(
            json_encode([StubClass2::class,  StubClass3::class])
        );

        $this->decorator->reflect();

        $classes = $this->decorator->getClasses();

        $this->assertEquals([
            new ReflectionClass(StubClass2::class),
            new ReflectionClass(StubClass3::class),
        ], $classes);
    }
}
