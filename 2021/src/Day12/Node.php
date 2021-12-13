<?php

namespace Ikari7789\Adventofcode\Year2021\Day12;

class Node
{
    public function __construct(
        private string $value,
        private array $linked = [],
    ) {

    }

    public function linkTo(Node $node, bool $reciprocal = true): self
    {
        if (! $this->linked($node)) {
            $this->linked[] = $node;
        }

        if ($reciprocal) {
            $node->linkTo($this, false);
        }

        return $this;
    }

    public function linked(Node $node): bool
    {
        foreach ($this->linked as $linkedNode) {
            if ($linkedNode->value === $node->value) {
                return true;
            }
        }

        return false;
    }

    public function notVisitedNodes($visitedValues)
    {
        $notVisitedNodes = [];

        foreach ($this->linked as $linkedNode) {
            if (! in_array($linkedNode->value, $visitedValues)) {
                $notVisitedNodes = $linkedNode;
            }
        }

        return $notVisitedNodes;
    }

    
}