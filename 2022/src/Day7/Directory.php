<?php

declare(strict_types=1);

namespace Ikari7789\Adventofcode\Year2022\Day7;

class Directory
{
    public array $children = [];

    public function __construct(
        public string $name,
        public ?Directory $parent = null
    ) { }

    public function addFile(string $name, int $size): File
    {
        $file = new File($name, $size, $this);

        $this->children[$name] = $file;

        return $file;
    }

    public function addDirectory(string $name): Directory
    {
        $directory = new Directory($name, $this);

        $this->children[$name] = $directory;

        return $directory;
    }

    public function size(): int
    {
        $size = 0;

        foreach ($this->children as $child) {
            $size += $child->size();
        }

        return $size;
    }

    public function __toString(): string
    {
        return $this->name . ' (dir, size=' . $this->size() . ')';
    }
}
