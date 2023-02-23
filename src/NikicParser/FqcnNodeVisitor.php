<?php

declare(strict_types=1);

namespace Jerowork\FileClassReflector\NikicParser;

use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\Interface_;
use PhpParser\Node\Stmt\Namespace_;
use PhpParser\Node\Stmt\Trait_;
use PhpParser\NodeVisitorAbstract;

final class FqcnNodeVisitor extends NodeVisitorAbstract
{
    private ?string $namespace  = null;
    private ?string $objectName = null;

    /**
     * @var null|class-string
     */
    private ?string $fqcn = null;

    public function enterNode(Node $node) : int|Node|null
    {
        if ($node instanceof Namespace_) {
            $this->namespace = (string) $node->name;
        }

        if ($node instanceof Class_ || $node instanceof Trait_ || $node instanceof Interface_) {
            $this->objectName = (string) $node->name;

            /** @var class-string $fqcn */
            $fqcn       = $this->objectName;
            $this->fqcn = $fqcn;
        }

        if ($this->namespace !== null && $this->objectName !== null) {
            /** @var class-string $fqcn */
            $fqcn = sprintf('%s\%s', $this->namespace, $this->objectName);

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
