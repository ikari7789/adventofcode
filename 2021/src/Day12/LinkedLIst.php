<?php

namespace Ikari7789\Adventofcode\Year2021\Day12;

class LinkedList
{
    public function __construct(
        protected Node $parent,
    ) {
        
    }

    public function parent()
    {
        return $this->parent;
    }

    public function find(string $value): ?string
    {
        return $this->findInList($value, $this->parent);
    }

    protected function findInList(string $value, ?Node $node): Node
    {
        if ($value === $node->value()) {
            return $node;
        }

        $found = null;
        foreach ($node->children() as $child) {
            $found = $this->findInList($value, $child);

            if ($found !== null) {
                break;
            }
        }

        return $found;
    }
}