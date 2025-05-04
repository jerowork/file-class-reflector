<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector\NikicParser;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Enum_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeVisitorAbstract;

final class FqcnNodeVisitor extends NodeVisitorAbstract
{
    private ?string $namespace = null;
    private ?string $className = null;

    /**
     * @var null|class-string
     */
    private ?string $fqcn = null;

    public function enterNode(Node $node) : array|int|Node|null
    {
        if ($node instanceof Namespace_) {
            $this->namespace = (string) $node->name;
        }

        if ($node instanceof Class_ || $node instanceof Trait_ || $node instanceof Interface_ || $node instanceof Enum_) {
            $this->className = (string) $node->name;

            /** @var class-string $fqcn */
            $fqcn       = $this->className;
            $this->fqcn = $fqcn;
        }

        if ($this->namespace !== null && $this->className !== null) {
            /** @var class-string $fqcn */
            $fqcn = sprintf('%s\%s', $this->namespace, $this->className);

            $this->fqcn = $fqcn;
        }

        return parent::enterNode($node);
    }

    /**
     * @return null|class-string
     */
    public function getFqcn() : ?string
    {
        return $this->fqcn;
    }
}
