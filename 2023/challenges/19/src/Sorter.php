<?php

declare(strict_types=1);

require_once 'Parts.php';
require_once 'WorkflowResult.php';
require_once 'Workflows.php';

class Sorter
{
    private array $processed;

    public function __construct(
        readonly private Parts $parts,
        readonly private Workflows $workflows,
    ) {
        $this->processed = [
            WorkflowResult::Accept->value => [],
            WorkflowResult::Reject->value => [],
        ];

        $this->sort();
    }

    public function processed(): array
    {
        return $this->processed;
    }

    public function accepted(): array
    {
        return $this->processed[WorkflowResult::Accept->value];
    }

    public function acceptedSum(): int
    {
        return array_sum(array_map(fn($part) => $part->sum(), $this->accepted()));
    }

    public function rejected(): array
    {
        return $this->processed[WorkflowResult::Reject->value];
    }

    private function sort(): void
    {
        foreach ($this->parts->parts() as $part) {
            $this->processed[$this->workflows->process($part)->value][] = $part;

            // readline();
        }
    }
}
